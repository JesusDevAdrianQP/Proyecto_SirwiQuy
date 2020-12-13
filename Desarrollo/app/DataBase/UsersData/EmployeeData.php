<?php

namespace App\DataBase\UsersData;

use App\Factory_Method\UserInterface;
use App\DataBase\UserDataMaster;
use Illuminate\Http\Response;
use App\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeData implements UserInterface
{
    public static function validationemail($mail): Bool{
        $response = Employee::where('email', '=', $mail)->first();
        
        if($response) return true;
        else return false;
    }

    public static function validationusername($username): Bool{
        $response = Employee::where('username', '=', $username)->first();
        
        if($response) return true;
        else return false;
    }

    public static function validationpass($param, $loger, $pass): Bool{
        $worker = EmployeeData::getUser($param, $loger);

        $response = Hash::check($pass, $worker->password);
        
        if($response) return true;
        else return false;
    }

    public static function getUser($par, $loger){
        if($par == 1) return Employee::where('email', '=', $loger)->first();
            else if($par == 2) return Employee::where('username', '=', $loger)->first();
             else return Employee::where('access', '=', $loger)->first();
    }

    public static function getToken($par, $loger){
        $token = EmployeeData::getUser($par, $loger);

        return $token->access;
    }

    public static function register($new_user){
        $access = new UserDataMaster();
        $employee = new Employee();
        
        $employee->email = $new_user->email;
        $employee->password = bcrypt($new_user->password);
        $employee->username = $new_user->username;
        $employee->access = $access->getCodigo(30);

        $employee->save();
    }

    public static function updatetoken($token){
        $access = new UserDataMaster();
        $user = EmployeeData::getUser(3, $token);

        if($user){
            $user->access = $access->getCodigo(30);
            $user->save();
        }
    }

    public static function update($user){
        $employee = EmployeeData::getUser(3, $user->token);
 
        if($employee){
            $employee->DNI = $user->dni;
            $employee->edad = $user->age;
            $employee->file = $user->photo;
            $employee->name = $user->first_name;
            $employee->lastnamep = $user->last_name_p;
            $employee->lastnamem = $user->last_name_m;
            $employee->departamento = $user->department;
            $employee->provincia = $user->province;
            $employee->distrito = $user->district;
            $employee->adress = $user->address;
            $employee->cuenta = $user->bank_account;
 
            $employee->save();

            return response()->json(['success' => ['Cambios guardados']], 200);
        }else return response()->json(['errors' => ['fail' => ['Hubo un error de conexión ... Intente más tarde']]], 422);
    }
}