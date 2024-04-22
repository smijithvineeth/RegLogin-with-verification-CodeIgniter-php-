<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $usersModel =new \App\Models\UsersModel();
        $loggedUserID= session()->get('loggedUser');
        $userInfo =$usersModel->find($loggedUserID);
        $data =[
            'title'=>'Dashboard',
            'userInfo'=>$userInfo
        ];
        return view('dashboard/index',$data);
    }
    function profile(){
        $usersModel =new \App\Models\UsersModel();
        $loggedUserID= session()->get('loggedUser');
        $userInfo =$usersModel->find($loggedUserID);
        $data =[
            'title'=>'profile',
            'userInfo'=>$userInfo
        ];
        return view('dashboard/profile',$data);

    }
}
