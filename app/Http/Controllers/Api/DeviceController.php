<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\DeviceRequest;
use App\Interfaces\Repositories\ProviderPosRepositoryInterface;
use App\Libraries\tables\DeviceTable;
use App\Models\Coordinate;
use App\Models\Device;
use App\Models\ProviderPos;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use LaravelEnso\VueDatatable\app\Traits\Datatable;
use LaravelEnso\VueDatatable\app\Traits\Excel;


class DeviceController extends Controller
{
    use Datatable, Excel;

    protected $tableClass = DeviceTable::class;
    private $repository;

    public function __construct(ProviderPosRepositoryInterface $providerPosRepository = null)
    {
        $this->repository = $providerPosRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $devices = Device::select('alias', 'id', 'imei', 'name' )
            ->where('provider_pos_id',null)    // выбать только не выбранные устройства
            ->get();
        $result = $this->repository->initArrayTreeSelectDevices ($devices);
        return response()->json([$result, 'success' => true],200);

        return Response::json(['error'=>'no data in'],404); // сформировать ответ с ошибкой
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeviceRequest $request)
    {
        $model = new  Device;
        $model->fill(collect($request)->toArray());
        $model->save();
        return response()->json([
            'id' => $model->id,
            'success' => true
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show ($id)
    {
        if($id) {
            $device = Device::query()
               ->with(['ProviderPos:id,provider_id', 'ProviderPos.provider:id,name'])
                ->where('id',$id) // ($id) // find
                ->firstOrFail();
            $device = collect($device->toArray());
            $device['provider_id'] = $device['provider_pos']['provider_id'];
            $device['provider_name'] = $device['provider_pos']['provider']['name'];
            unset($device['provider_pos']);

            return response()->json($device,200);
        }
        return Response::json([
            'errors' => 'Not id method show in ProviderController',
            'errorType'=>'REST_ERROR'
        ],404); // сформировать ответ с ошибкой
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeviceRequest $request, $id)
    {
//        $validated = $request->validated();
//        $this->validate($request,$rules,$messges) ; // получить валидацию

        $model = Device::findOrFail($id);
        $model->fill(collect($request)->toArray());
        $model->save();
        $changes = $model->getChanges();
            return response()->json([
                'item' => $changes,
                'success' => true
            ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id, &$dev) {
            // очистить   список устройств
            $clear = $this->repository->clearDevice([$id]);
            $dev = Device::destroy($id);
            if($dev) {
                DB::commit();
            }else{
                DB::rollBack();
            }
        });
        return ['status' => 'success delete ' . $dev. ' rows'  ];

    }

    /**
     * Вывод трека устройства
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tracks(Request $request, $id)
    {
        $date = $request->date;
        $query = Coordinate::query()->where(['device_id' => $id]);

        if($date && $date[0] && $date[1]){
            $begin_date = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z',
                $date[0]);
            $end_date = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z',
                $date[1]);

            $query->whereBetween('created_at', [$begin_date, $end_date]);
        }else{
            $query->limit(50);
        }

        return $query->get()->toArray();
    }


    /**
     * Используется при валидации добавления трекера на карту в клиентской части
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function validateAlias($alias)
    {
        $device = Device::query()->where(['alias' => $alias])->first();
        if($device){return response(['error' => null]);}
        return response(['error' => 'Трекер не найден']);
    }
}
