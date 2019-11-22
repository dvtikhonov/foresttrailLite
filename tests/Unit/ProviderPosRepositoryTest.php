<?php

namespace Tests\Unit;

use App\Models\Device;
use App\Models\ProviderPos;
use App\Models\TransferDevice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;
use Mockery as m;

class ProviderPosRepositoryTest extends TestCase
{
    private $repository;
    private $providerPos;
    private $device;

    public function setUp()
    {
        parent::setUp(); //
// вставить зависимости без конструктора
        $this->repository = 'App\Interfaces\Repositories\ProviderPosRepositoryInterface';
        $this->providerPos = ProviderPos::class;
        $this->device = Device::class;

    }

    public function tearDown()
    {
        dump(__METHOD__, 'Down');
        \Mockery::close();
//        Test::clean();
    }

    /**
     * Проверка  метода initArrayTreeSelectPos
     *
     * @test
     */
    public function test_initArrayTreeSelectPos()
    {
        // сгенерировать Eloquent Collection  из 4 записей
        $providerPos = factory($this->providerPos, 4)
            ->make()
            ->each(function ($providerPos, $key)  {
                $providerPos->provider_id = 3;
                $providerPos->id = $key+1;
                $providerPos->balance = 0;
                $providerPos->is_blocked = random_int(0,1);
                $providerPos->deleted_at = null;
                $providerPos->created_at = null;
                $providerPos->updated_at = null;
            });
        $rep_initArrayTreeSelectPos = \App::make($this->repository);

        $result = $rep_initArrayTreeSelectPos->initArrayTreeSelectPos($providerPos);
//        $this->expectException('$this->repository');

        $pos_in = $providerPos->toArray();
        $pos_in_id = [ $pos_in[0]['id'], $pos_in[1]['id'], $pos_in[2]['id'],$pos_in[3]['id']];
        $pos_in_tmp = [
            ['key' => $pos_in[0]['id'], 'name' => $pos_in[0]['name']],
            ['key' => $pos_in[1]['id'], 'name' => $pos_in[1]['name']],
            ['key' => $pos_in[2]['id'], 'name' => $pos_in[2]['name']],
            ['key' => $pos_in[3]['id'], 'name' => $pos_in[3]['name']],
            ];

        $this->assertEqualsCanonicalizing($result['pos_id'], $pos_in_id);
        $this->assertEqualsCanonicalizing($result['pos_tmp']->toArray(), $pos_in_tmp);
        $this->assertEqualsCanonicalizing($result[2], $pos_in[2]);

    }

    /**
     * Проверка  метода initArrayTreeSelectDevices
     *
     * @test
     */
    public function test_initArrayTreeSelectDevices()
    {
        // сгенерировать Eloquent Collection  из 4 записей
        $device = factory($this->device, 4)
            ->make()
            ->each(function ($device, $key) {
                $device->provider_pos_id = random_int(1, 12);
                $device->id = $key + 1;
                $device->deleted_at = null;
                $device->created_at = null;
                $device->updated_at = null;
            });

        $rep_initArrayTreeSelectDevices = \App::make($this->repository);

        $result = $rep_initArrayTreeSelectDevices->initArrayTreeSelectDevices($device);

        $device_in = $device->toArray();
        $device_in_tmp = [
            ['key' => $device_in[0]['id'], 'name' => $device_in[0]['alias'].'  '. $device_in[0]['name'] ],
            ['key' => $device_in[1]['id'], 'name' => $device_in[1]['alias'].'  '. $device_in[1]['name'] ],
            ['key' => $device_in[2]['id'], 'name' => $device_in[2]['alias'].'  '. $device_in[2]['name'] ],
            ['key' => $device_in[3]['id'], 'name' => $device_in[3]['alias'].'  '. $device_in[3]['name'] ],
        ];

        $this->assertEqualsCanonicalizing($result->toArray(), $device_in_tmp);

    }

    /**
     * Проверка  метода initArrayTreeSelect
     *
     * @test
     */
    public function test_initArrayTreeSelect()
    {

        $providerPos = $this->ModelProviderPosWithDevices();
//        $this->expectException('$this->repository');
        $devices_in = $providerPos->devices->toArray();
        $devices_in_tmp = [
            ['key' => $devices_in[0]['id'], 'name' => $devices_in[0]['alias'].'  '. $devices_in[0]['name'] ],
            ['key' => $devices_in[1]['id'], 'name' => $devices_in[1]['alias'].'  '. $devices_in[1]['name'] ],
        ];
        $devices_in_id = [$devices_in[0]['id'], $devices_in[1]['id']];

//        dd($providerPos->list_items);
        $rep_initArrayTreeSelect = \App::make($this->repository, [
            'providerPos' => $providerPos,
        ]);

        $result = $rep_initArrayTreeSelect->initArrayTreeSelect($providerPos);

        $this->assertEqualsCanonicalizing($result->devices_tmp->toArray(), $devices_in_tmp);
        $this->assertEqualsCanonicalizing($result->devices_id, $devices_in_id);

    }
    /**
     * Проверка  метода getDevicePos
     *
     * @test
     */
    public function test_getDevicePos()
    {
        $id = $this->ModelProviderPosWithDevices()->id;
        $providerPos = $this->getMockBuilder(ProviderPos::class)
            ->setMethods(['newQuery'])
            ->getMock();
        $query = m::mock(Builder::class)->makePartial();
        $query->shouldReceive('where')
            ->with(['id'=>$id])
            ->once()->andReturn($query);
        $query->shouldReceive('first')
            ->andReturn($this->ModelProviderPosWithDevices());
        $providerPos->expects($this->once())
            ->method('newQuery')
            ->will($this->returnValue($query));//$this->returnValue($query)
        $providerPos->id = $id;
//        $rep_getDevice = new $this->repository($providerPos);
        $rep_getDevice = \App::make($this->repository, [
            'providerPos' => $providerPos,
//            'device' => null,
//            'transferDevice' => null
        ]);


        $result = $rep_getDevice->getDevicePos( $id);
        $devices_in_id = $this->ModelProviderPosWithDevices()->devices
            ->pluck('id')->toArray();
        $this->assertEqualsCanonicalizing($result, $devices_in_id);
//        dd ($result, $devices_in_id);

    }

    /**
     * Проверка  метода clearDevice
     *
     * @test
     */
    public function test_clearDevice()
    {

        $device = $this->getMockBuilder(Device::class)
            ->setMethods(['newQuery'])
            ->getMock();
        $transferDevice = $this->getMockBuilder(TransferDevice::class)
            ->setMethods(['newQuery'])
            ->getMock();
        $queryDevice = m::mock(QBuilder::class)->makePartial();
        $queryTrans = clone $queryDevice;

        $queryDevice->shouldReceive('update')->with(['provider_pos_id' => null])->andReturn($device);
        $queryTrans->shouldReceive('update')->andReturn(true);

        $transferDevice->expects($this->once())->method('newQuery')->will($this->returnValue($queryTrans));
        $device->expects($this->once())->method('newQuery')->will($this->returnValue($queryDevice)); // $this->at(10)

        $rep_clearDevice = \App::make($this->repository, [
//            'providerPos' => null,
            'device' => $device,
            'transferDevice' => $transferDevice
        ]);

        $result = $rep_clearDevice->clearDevice( [3]);

//        dd ( $result);

        $this->assertTrue(  $result);
//        dd ($result, $devices_in_id);

    }

/**
 * Mock для модели  ProviderPos с reletions  devices
 *
 */

    public function ModelProviderPosWithDevices()
    {
        $relation = $this->getRelation();
        $result1 = new Device;
        $result1->setTable('devices');
        $result1->setConnection('mysql');
        $result1->id = 5;
        $result1->provider_pos_id = 1;
        $result1->name = 'Нименование устройства №1';
        $result1->alias = 'Псевдоним устройства №1';
        $result1->options = 'Опции устройства №1';
        $result1->device_tariff_id = 2;
        $result1->imei = '35-567-3454';
        $result1->deleted_at = null;
        $result1->created_at = date('Y-m-d H:i:s');
        $result1->updated_at = date('Y-m-d H:i:s');
        $result1->foreign_key = 1;
        $result2 = new Device;
        $result2->setTable('devices');
        $result2->setConnection('mysql');
        $result2->id = 3;
        $result2->provider_pos_id = 1;
        $result2->name = 'Нименование устройства №2';
        $result2->alias = 'Псевдоним устройства №2';
        $result2->device_tariff_id = 3;
        $result2->imei = '35-567-3457';
        $result2->deleted_at = null;
        $result2->created_at = date('Y-m-d H:i:s');
        $result2->updated_at = date('Y-m-d H:i:s');
        $result2->foreign_key = 1;
        $result3 = new EloquentHasManyModelStub; // не используется в данной конструкции
        $result3->foreign_key = 2;
        $model1 = new ProviderPos;
        $model1->setTable('provider_pos');
        $model1->setConnection('mysql');
//        $model1->syncOriginal(); // синхронизация  атрибутов и оригинала
//        $model1->name = 'taylor'; // пример
//        $model1->exists = true; // модель существует
        $model1->id = 1;
        $model1->provider_id = 1;
        $model1->name = 'Имя точки продаж №1';
        $model1->address = 'Россия, Кемеровская область, Новокузнецк, улица Кирова, 62';
        $model1->lon = 87.148074978583;
        $model1->lat = 53.757023450487;
        $model1->contacts = 'Контакты С. Петя';
        $model1->phone = 12345678910;
        $model1->balance = 0.0;
        $model1->is_blocked = 0;
        $model1->minimum_balance = 78.0;
        $model1->deleted_at = null;
        $model1->created_at = date('Y-m-d H:i:s');
        $model1->updated_at = date('Y-m-d H:i:s');
        $model2 = new EloquentHasManyModelStub; // в текуще конструкции не используется
        $model2->id = 2;
        $model3 = new EloquentHasManyModelStub; // в текуще конструкции не используетс
        $model3->id = 3;
        $relation->getRelated()->shouldReceive('newCollection')->andReturnUsing(function ($array) {
            return new Collection($array);
        });
//        $models = $relation->match([$model1, $model2, $model3], new Collection([$result1, $result2, $result3]), 'foo');
        $models = $relation->match([$model1], new Collection([$result1, $result2]), 'devices');

        return $models[0];

    }

    protected function getRelation()
    {
        $builder = m::mock(Builder::class);
        $builder->shouldReceive('whereNotNull')->with('table.foreign_key');
        $builder->shouldReceive('where')->with('table.foreign_key', '=', 1);
        $related = m::mock(Model::class);
        $builder->shouldReceive('getModel')->andReturn($related);
        $parent = m::mock(Model::class);
        $parent->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $parent->shouldReceive('getCreatedAtColumn')->andReturn('created_at');
        $parent->shouldReceive('getUpdatedAtColumn')->andReturn('updated_at');
        return new HasMany($builder, $parent, 'table.foreign_key', 'id');
    }
}

class EloquentHasManyModelStub extends Model
{
    public $foreign_key = 'foreign.value';
}

