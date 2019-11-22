<?php

namespace App\Libraries;


use App\Interfaces\GenerateDeviceReportInterface;
use App\Interfaces\Repositories\ProviderPosPaymentRepositoryInterface;
use App\Models\Coordinate;
use App\Models\Device;
use App\Models\DevicesReport;
use App\Models\DevicesReportTrack;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateDeviceReport implements GenerateDeviceReportInterface
{
    // кол-во полезных секунд (когда трекер отправил координаты) после которых считается что трекер закончил работать
    const CLOSE_PERIOD = 300;

    private $providerPosPaymentRepository;

    public function __construct(ProviderPosPaymentRepositoryInterface $providerPosPaymentRepository)
    {
        $this->providerPosPaymentRepository = $providerPosPaymentRepository;
    }

    public function start(\DateTime $date = null, $forceRefresh = true)
    {
        Log::info('Start generate devices report');

        if(!$date){ $date = new \DateTime(); }

        $startDate = ($date)->modify('today');
        $endDate = (clone $startDate)->modify('front of 24');

        $periodCoordinates = DB::query()->from('coordinates')
            ->select(['*', 'coordinates.created_at as created_at', 'coordinates.id as id'])
            ->join('transfer_devices', function($join){
                $join->on('transfer_devices.device_id', '=', 'coordinates.device_id')
                    ->whereRaw(DB::raw('transferred_at <= coordinates.created_at AND (returned_at >= coordinates.created_at OR returned_at is null)'));
            })
            ->whereNotExists(function($query){
                $query->select(DB::raw(1))
                    ->from('provider_pos_block_dates')
                    ->whereRaw('provider_pos_block_dates.provider_pos_id = transfer_devices.provider_pos_id')
                    ->whereRaw('blocked_at <= coordinates.created_at AND (restored_at >= coordinates.created_at OR restored_at is null)');
            })
            ->orderBy('coordinates.created_at')
            ->whereBetween('coordinates.created_at', [$startDate, $endDate])
            ->get();

        $grouped = $periodCoordinates
            ->groupBy('device_id');

        $devices = Device::query()->findMany($grouped->keys());
        $oldReports = DevicesReport::query()
            ->whereBetween('report_at', [$startDate, $endDate])
            ->get();

        $grouped
            ->each(function ($coordinates, $device_id) use ($devices, $oldReports, $forceRefresh, $endDate){
                $device = $devices->firstWhere('id', $device_id);

                $tariff_amount = $device->deviceTariff->amount;
                $provider_pos_minutes = $this->calculateMinutes($coordinates);
                foreach ($provider_pos_minutes as $provider_pos_id => $details){

                    $minutes = $details['minutes'];
                    $total = $minutes * $tariff_amount;

                    $report = $oldReports->where('device_id', $device_id)->where('provider_pos_id', $provider_pos_id)->first();
                    if( $report && !$forceRefresh) { return; }

                    $attributes = [
                        'device_id' => $device_id,
                        'provider_pos_id' => $provider_pos_id,
                        'minutes' => $minutes,
                        'tariff_amount' => $tariff_amount,
                        'total' => $total,
                        'report_at' => $endDate
                    ];
                    if($report){
                        $lastPayment = $report->payments->last();
                        $report->fill($attributes);
                        $report->tracks()->forceDelete();
                        if($lastPayment) {
                            $this->returnWithdraw($provider_pos_id, $lastPayment->amount, $report->id, $lastPayment->id);
                        }
                    }else{
                        $report = new DevicesReport($attributes);
                    }
                    $report->save();
                    $this->withdraw($provider_pos_id, $total, $device->alias, $report->id, $endDate->format('d.m.Y'));

                    $report->tracks()->saveMany($details['tracks']);
                }
            });

        Log::info('End generate devices report');
        return true;
    }

    private function withdraw($provider_pos_id, $total, $device_alias, $report_id, $report_at){
        $this->providerPosPaymentRepository
            ->withdraw($provider_pos_id, $total, 'Использование трекера '.$device_alias.' за ' . $report_at, $report_id);
    }

    private function returnWithdraw($provider_pos_id, $total, $report_id, $payment_id){
        $this->providerPosPaymentRepository
            ->deposit($provider_pos_id, $total, 'Перерасчет - возврат списания №'.$payment_id, $report_id);
    }

    private function calculateMinutes( Collection $mixedCoordinates)
    {
        $result = [];

        $mixedCoordinates->groupBy('provider_pos_id')
            ->each(function (Collection $coordinates, $provider_pos_id) use (&$result) {
                $startCoordinate = null;
                $lastCoordinate = null;

                $result[$provider_pos_id] = [
                    'tracks' => [],
                    'minutes' => 0
                ];

                $coordinates->each(function ($coordinate, $key) use (&$result, &$lastCoordinate, &$startCoordinate, $coordinates, $provider_pos_id){
                    if($lastCoordinate) {
                        $interval = Carbon::parse($lastCoordinate->created_at)->diff(Carbon::parse($coordinate->created_at));
                        $intervalSeconds = $interval->i * 60 + $interval->s;

                        // Если предыдущая координата была ранее 5 минут назад вычисляем кол-во минут
                        if( $intervalSeconds > self::CLOSE_PERIOD || $key === $coordinates->count() - 1 ){

                            if($key === $coordinates->count() - 1){$lastCoordinate = $coordinate;}
                            $allInterval = Carbon::parse($startCoordinate->created_at)->diff(Carbon::parse($lastCoordinate->created_at));
                            $seconds = $allInterval->i * 60 + $allInterval->s;
                            $result[$provider_pos_id]['minutes'] += $seconds/60;

                            $result[$provider_pos_id]['tracks'][] = new DevicesReportTrack([
                                'start_at' => $startCoordinate->created_at,
                                'end_at' => $lastCoordinate->created_at
                            ]);

                            $startCoordinate = $coordinate;
                        }

                        $lastCoordinate = $coordinate;
                    }else{
                        $lastCoordinate = $coordinate;
                        $startCoordinate = $coordinate;
                    }
                });

                $result[$provider_pos_id]['minutes'] = round($result[$provider_pos_id]['minutes']);
            });

        return $result;
    }

    public function test($force = false)
    {
        dd('test');
    }
}