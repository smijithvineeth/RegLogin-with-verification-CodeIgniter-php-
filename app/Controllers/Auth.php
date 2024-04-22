<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Libraries\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Auth extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
        $this->email = \Config\Services::email();
    }

    public function index()
    {
        return view('auth/login');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function save()
    {
        $validation = $this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Your full name is required'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'You must enter a valid email',
                    'is_unique' => 'Email already exists'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[5]|max_length[12]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Min 5 characters required',
                    'max_length' => 'Max 12 characters required'
                ]
            ],
            'cpassword' => [
                'rules' => 'required|min_length[5]|max_length[12]|matches[password]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Min 5 characters required',
                    'max_length' => 'Max 12 characters required',
                    'matches' => 'Passwords do not match'
                ]
            ]
        ]);
    
        if (!$validation) {
            return view('auth/register', ['validation' => $this->validator]);
        } else {
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
    
            $otp = mt_rand(100000, 999999);
    
            $values = [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'otp' => $otp, 
                'otp_expires_at' => date('Y-m-d H:i:s', strtotime('+5 minutes')), 
            ];
    
            $usersModel = new \App\Models\UsersModel();
        $query = $usersModel->insert($values);

        if (!$query) {
            return redirect()->back()->with('fail', 'Something went wrong');
        } else {
           
            session()->set('verify_email', $email);

           
            $this->sendVerificationEmail($email, $otp);

            return redirect()->to('/auth/verifyOTP');
        }
    }
}
    
    


public function verifyOTP()
{
    $sessionEmail = session()->get('verify_email'); 

    if ($sessionEmail) {
        $validation = $this->validate([
            'otp' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'OTP is required'
                ]
            ]
        ]);

        if ($validation) {
            $otp = $this->request->getPost('otp');

            $usersModel = new \App\Models\UsersModel();
            $user = $usersModel->where(['email' => $sessionEmail, 'otp' => $otp])->first();

            if ($user && strtotime($user['otp_expires_at']) >= time() && $otp === $user['otp']) {
                
                try {
                    $updateData = [
                        'is_verified' => 1,
                        'otp' => null,
                        'otp_expires_at' => null
                    ];
                    $affectedRows = $usersModel->update($user['id'], $updateData);

                    if ($affectedRows) {
                        session()->setFlashdata('success', 'Account created and verified. Please log in.');
                        return redirect()->to('/auth');
                    } else {
                        throw new \Exception('Update failed: No rows affected.');
                    }
                } catch (\Exception $e) {
                    
                    log_message('error', 'Verification Update Error: ' . $e->getMessage());

                    session()->setFlashdata('fail', 'Account verification failed. Please try again.');
                }
            } else {
                session()->setFlashdata('fail', 'Invalid OTP or OTP expired.');
            }
        }
    }

    return view('auth/verifyOTP', ['validation' => $this->validator]);
}






    private function sendVerificationEmail($email, $otp)
{
    $subject = 'Account Verification';
    $message = 'Please click the following link to verify your email: ' . $otp;

    $this->email->setTo($email);
    $this->email->setFrom('lroter4@gmail.com', 'winsafe'); 
    $this->email->setSubject($subject);
    $this->email->setMessage($message);

    if ($this->email->send()) {
        echo "Email sent successfully to $email";
    } else {
        echo "Email sending failed. Error: {$this->email->printDebugger(['headers'])}";
    }
}
    


function check() {
    $validation = $this->validate([
        'email'=>[
            'rules'=>'required|valid_email|is_not_unique[users.email]',
            'errors'=>[
                'required'=>'email is required',
                'valid_email'=>'you must enter a valid mail',
                'is_not_unique'=>'This mail is not registered'
            ]
        ],
        'password'=>[
            'rules'=>'required|min_length[5]|max_length[12]',
            'errors'=>[
                'required'=>'password is required',
                'min_length'=>'min 5 characters required',
                'max_length'=>'max 5 characters required'
            ]
        ]
    ]);

    if (!$validation) {
        return view('auth/login',['validation'=>$this->validator]);
    } else {
        $email=$this->request->getPost('email');
        $password=$this->request->getPost('password');
        $usersModel = new \App\Models\UsersModel();
        $user_info = $usersModel->where('email', $email)->first();

        if (!$user_info || !Hash::check($password, $user_info['password'])) {
            session()->setFlashdata('fail','Incorrect email or password');
            return redirect()->to('/auth')->withInput();
        }

      
        if (!$user_info['is_verified']) {
            session()->setFlashdata('fail','You must verify your account first.');
            return redirect()->to('/auth')->withInput();
        } else {
            $user_id = $user_info['id'];
            session()->set('loggedUser', $user_id);
            return redirect()->to('/dashboard/profile');
        }
    }
}
    function logout(){
        if(session()->has('loggedUser')){
            session()->remove('loggedUser');
            return redirect()->to('/auth?access=out')->with('fail','you are logged out');
        }
    }
}