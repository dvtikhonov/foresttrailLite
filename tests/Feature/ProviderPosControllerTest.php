<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Provider;
use App\Models\ProviderPos;
use App\Models\TransferDevice;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProviderPosControllerTest extends TestCase

{

    public static $upBefore = false;

    public function setUp()
    {
        parent::setUp(); //
//        $this->mock('App\Interfaces\Repositories\ProviderPosRepositoryInterface');
    }

    public function tearDown()
    {
        dump(__METHOD__, 'Down');
        \Mockery::close();
    }

    public static function setUpBeforeClass(): void
    {
        static::$upBefore = true;
    }

    /**
     * Проверка валидации
     *
     * @test
     * @dataProvider nameInputValidation
     * @dataProvider addressInputValidation
     * @dataProvider phoneInputValidation
     */
    public function test_form_validation($formInput, $formInputValue)
    {
        $user = User::find(rand(1, 3));
        Auth::loginUsingId($user->id); //

        $user_id = $user->id;
        $token = $user->createToken('api')->accessToken;
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('POST', 'api/v1/provider-pos/', [
            $formInput => $formInputValue,
        ],  $headers);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function nameInputValidation()
    {
        return [
            'name is required' => ['name', ''],
            'contacts is required' => ['contacts', ''],
        ];
    }

    public function addressInputValidation()
    {
        return [
            'address is required' => ['address', ''],
        ];
    }

    public function phoneInputValidation()
    {
        return [
            'phone is required' => ['phone', ''],
            'phone must be valid: required|numeric|min:10' => ['phone', 9],
        ];
    }

    /**
     * Проверка функциональная API .
     * по маршруту provider-pos
     *
     * @test
     * @return void
     */
    public function testProviderPosIndex()
    {
//        $this->mock->shouldReceive('clearDevice')->once()->andReturn(49);

        $user = User::find(rand(1, 3));
        Auth::loginUsingId($user->id); //

        $user_id = $user->id;
        $token = $user->createToken('api')->accessToken;
        $headers = ['Authorization' => "Bearer $token"];
        $providerId = (rand(1, Provider::count()));

        //        Auth::logout();
        $response = $this// проверка авторизации
        ->json('GET', 'api/v1/provider-pos/', [], ['Authorization' => "Bearer $token" . 'BadToken'])
            ->assertStatus(400)
            ->assertJson([
                'errors' => 'Unauthenticated.',
                'exception' => 'Illuminate\\Auth\\AuthenticationException',
            ]);

        $response = $this     // проверка хорошие данные
            ->json('GET', 'api/v1/provider-pos/', ['provider_id' =>$providerId], $headers)
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure([ 0 =>[
                '0' => ['id', 'provider_id', 'name', 'address', 'lon', 'lat', 'contacts', 'phone', 'balance', 'minimum_balance',
                    'is_blocked', 'deleted_at', 'created_at', 'updated_at'],
                'pos_tmp' => ['*' => ['key', 'name']],
                'pos_id' => []
            ]])
            ->assertJson(['success' => true]);
        $response = $this// проверка
            ->json('GET', 'api/v1/provider-pos/'  , ['provider_id' =>$providerId+100], $headers)
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure([ 0 =>[
                'pos_tmp' => [],
                'pos_id' => []
            ]])
            ->assertJson(['success' => true]);
    }

    /**
     * Проверка функциональная API .
     * по маршруту provider-pos
     *
     * @test
     * @return void
     */
    public function testProviderPosStore()
    {
        $user = User::find(rand(1, 3));
        Auth::loginUsingId($user->id); //

        $user_id = $user->id;
        $token = $user->createToken('api')->accessToken;
        $headers = ['Authorization' => "Bearer $token"];
        $provider_pos_id = rand(1, 12);

        //        Auth::logout();
        $response = $this// проверка авторизации
        ->json('POST', 'api/v1/provider-pos/', [], ['Authorization' => "Bearer $token" . 'BadToken'])
            ->assertStatus(400)
            ->assertJson([
                'errors' => 'Unauthenticated.',
                'exception' => 'Illuminate\\Auth\\AuthenticationException',
            ]);

//        DB::beginTransaction();

        $providerId = rand(1, 3);
        $providerPos = factory(\App\Models\ProviderPos::class, 1)
            ->make()
            ->each(function ($providerPos) use ($providerId) {
                $providerPos->provider_id = $providerId;
                // $session->save();
            })->toArray();

        $devices = Device::query()->where('provider_pos_id',null)->pluck('id')->toArray();
        $key_rand = array_rand($devices, 4);
        $devicesId = array();
        foreach ($key_rand as $key => $subArr) {
            array_push($devicesId, $devices[$subArr]);
        }
        $providerPos[0]['devices_id'] = $devicesId;

        $response = $this// проверка хорошие данные
        ->json('POST', 'api/v1/provider-pos/', $providerPos[0], $headers)
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/json')
            ->assertJson([
                'success' => 'tue',
    ]);
        $provider_pos_id = $providerPos[0]['id'] = $response->getOriginalContent()['id'];
//        dump ($devicesId, '$devicesId store ='.$provider_pos_id);
        $this->chekProviderPos($provider_pos_id,$providerPos[0], $devicesId );

//        DB::rollBack();

//
//        dump($response->getOriginalContent()['id']);
//        unset($providerPos[0]['devices_id']);
//        unset($providerPos[0]['is_blocked']); // проверить в observer
//        unset($providerPos[0]['minimum_balance']); // проверить в observer
//        unset($providerPos[0]['balance']); // проверить в observer
//        $providerPos[0]['id'] = $provider_pos_id;
//
//        $this->alterassertDatabaseHas($provider_pos_id,$providerPos[0]);
//
//        foreach ($devicesId as $key => $subArr) {
//            $this->assertDatabaseHas('devices',
//                ['id' => $subArr, 'provider_pos_id' =>$provider_pos_id]);
//        }
    }

    /**
     * Проверка функциональная API .
     * по маршруту provider-pos
     *
     * @test
     * @return void
     */
    public function testProviderPosUpdate()
    {
        $user = User::find(rand(1, 3));
        Auth::loginUsingId($user->id); //

        $user_id = $user->id;
        $token = $user->createToken('api')->accessToken;
        $headers = ['Authorization' => "Bearer $token"];
        $provider_pos_id = rand(1, 12);

        //        Auth::logout();
        $response = $this// проверка авторизации
        ->json('PUT', 'api/v1/provider-pos/' . $provider_pos_id, [], ['Authorization' => "Bearer $token" . 'BadToken'])
            ->assertStatus(400)
            ->assertJson([
                'errors' => 'Unauthenticated.',
                'exception' => 'Illuminate\\Auth\\AuthenticationException',
            ]);

//        DB::beginTransaction();
        $providerId = rand(1, 3);
        $providerPos = factory(\App\Models\ProviderPos::class, 1)
            ->make()
            ->each(function ($providerPos) use ($providerId) {
                $providerPos->provider_id = $providerId;
                // $session->save();
            })->toArray();

        $devices = Device::query()
            ->whereIn('provider_pos_id', [$provider_pos_id])
            ->orWhereNull('provider_pos_id')
            ->pluck('id')
            ->toArray();
        dump ($devices, '$devicesId update, мимнмальное количество 3 шт. provider_pos_id='. $provider_pos_id);
        $key_rand = array_rand($devices, 3);
        $devicesId = array();
        foreach ($key_rand as $key => $subArr) {
            array_push($devicesId, $devices[$subArr]);
        }
        $providerPos[0]['devices_id'] = $devicesId;

        $response = $this// проверка хорошие данные
        ->json('PUT', 'api/v1/provider-pos/' . $provider_pos_id, $providerPos[0], $headers)
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/json');

        $this->chekProviderPos($provider_pos_id,$providerPos[0], $devicesId );

//        DB::rollBack();

    }

    /**
     * Проверка функциональная API .
     * по маршруту provider-pos
     *
     * @test
     * @return void
     */
    public function testProviderPosShow()
    {
        $user = User::find(rand(1, 3));
        Auth::loginUsingId($user->id); //

        $user_id = $user->id;
        $token = $user->createToken('api')->accessToken;
        $headers = ['Authorization' => "Bearer $token"];
        $provider_pos_id = rand(1, 12);

        //        Auth::logout();
        $response = $this// проверка авторизации
        ->json('GET', 'api/v1/provider-pos/' . $provider_pos_id, [], ['Authorization' => "Bearer $token" . 'BadToken'])
            ->assertStatus(400)
            ->assertJson([
                'errors' => 'Unauthenticated.',
                'exception' => 'Illuminate\\Auth\\AuthenticationException',
            ]);

        $response = $this// проверка хорошие данные
        ->json('GET', 'api/v1/provider-pos/' . $provider_pos_id, [], $headers)
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJsonStructure([
                'id', 'provider_id', 'name', 'address', 'lon', 'lat', 'contacts', 'phone', 'balance', 'minimum_balance',
                'is_blocked', 'deleted_at', 'created_at', 'updated_at',
                'devices_tmp' => ['*' => ['key', 'name']],
                'devices_id' => []
            ])
            ->assertHeader('content-type', 'application/json')
            ->assertJson(['id' => $provider_pos_id]);

        $response = $this// проверка
        ->json('GET', 'api/v1/provider-pos/' . ($provider_pos_id + 100), [], $headers)
            ->assertStatus(400)
            ->assertHeader('content-type', 'application/json')
            ->assertSeeText('Trying to get property \'devices\' of non-object')
            ->assertJson([
                'exception' => 'ErrorException',
            ]);
    }

    /**
     * Проверка функциональная API .
     * по маршруту provider-pos
     *
     * @test
     * @return void
     */
    public function testProviderPosDestroy()
    {
        $user = User::find(rand(1, 3));
        Auth::loginUsingId($user->id); //

        $user_id = $user->id;
        $token =  $user->createToken('api')->accessToken;
        $headers = ['Authorization' => "Bearer $token"];
        $provider_pos_id = rand(1, 12);

        $response = $this               // проверка авторизации
        ->json('DELETE', 'api/v1/provider-pos/' . $provider_pos_id, [], ['Authorization' => "Bearer $token".'BadToken'])
            ->assertStatus(400)
            ->assertJson([
                'errors' => 'Unauthenticated.',
                'exception' => 'Illuminate\\Auth\\AuthenticationException',
            ]);

        $provider_pos_list = ProviderPos::query()
            ->where(['id' => $provider_pos_id])
            ->with('transferDevice:*')   //provider_pos_id,returned_at,transferred_at,device_id
            ->with('devices:*')          //provider_pos_id,id,name,device_tariff_id,imei,alias,options
            ->first();

        $device_ids = $provider_pos_list->devices
            ->pluck('id')
            ->toArray();
        $transferDevice_ids = $provider_pos_list->transferDevice
            ->pluck('id')
            ->toArray();

        $response = $this               // проверка
            ->json('DELETE', 'api/v1/provider-pos/' . $provider_pos_id,[], $headers)
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJson(['status' => 'success delete 1 rows']);
        $this->assertSoftDeleted('provider_pos', ['id' => $provider_pos_id]);
        $this->assertDatabaseMissing('transfer_devices', [
            'provider_pos_id' => $provider_pos_id,
            'returned_at' => null,
        ]);

        $this->assertDatabaseMissing('devices', [
            'provider_pos_id' => $provider_pos_id,
        ]);

        // Проверка на изменение содержания не редактируемых полей в записях Устройств
        $devices_list = Device::query()
            ->whereIn('id', $device_ids)
            ->get()
            ->toArray();
        $devices_cur_list = $provider_pos_list->devices->toArray();
        $this->clear_fields($devices_cur_list,$devices_list,'provider_pos_id');
        $this->assertArraySubset($devices_list, $devices_cur_list);

        // Проверка на изменение содержания не редактируемых полей в записях журнала премещенния Устройств
        $transferDevice_list = TransferDevice::query()
            ->whereIn('id', $transferDevice_ids)
            ->get()
            ->toArray();
        $transferDevice_cur_list = $provider_pos_list->transferDevice->toArray();
        $this->clear_fields($transferDevice_cur_list, $transferDevice_list,'returned_at' );
        $this->assertArraySubset($transferDevice_list, $transferDevice_cur_list);
    }


    public static function clear_fields(&$arrayA, &$arrayB,$name = null) {
        foreach ($arrayA as $key => $subArr) {      // удалить элемент  по  ключу  в подмасиве
            unset($arrayA[$key][$name]);
            unset($arrayA[$key]['updated_at']);
            unset($arrayB[$key][$name]);
            unset($arrayB[$key]['updated_at']);
        }
    }

    public function chekProviderPos($id,$arr_in, $devicesId )
    {
        unset($arr_in['devices_id']);
        unset($arr_in['is_blocked']); // проверить в observer
        unset($arr_in['minimum_balance']); // проверить в observer
        unset($arr_in['balance']); // проверить в observer
        $arr_in['id'] = $id;

        $this->alterassertDatabaseHas($id,$arr_in);

        foreach ($devicesId as $key => $subArr) {
            $this->assertDatabaseHas('devices',
                ['id' => $subArr, 'provider_pos_id' => $id]);
        }
        // dd(__METHOD__, $devicesId);
    }

    public function alterassertDatabaseHas($id,$arr_in )
    {
        $providerPos_cur = ProviderPos::find($id)->toArray();

        $diff = array_diff($arr_in, $providerPos_cur);
        $mesdiff = "Erros value(s) = " . implode(" , ", $diff); ;
        $this->assertEmpty ($diff, $mesdiff );

    }

}
