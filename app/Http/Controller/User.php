<?php

namespace App\Http\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserService;
use App\Service\BernardService;
use App\Model\User as UserModel;

class User extends Api
{
    protected $actions = [
        'change', 'login', 'resetpass', 'commission', 'me'
    ];

    protected function getService()
    {
        return $this->get(UserService::class);
    }

    protected function getAclData(): array
    {
        return [
            'get' => ['read', 'account'],
            'post' => ['create', 'account'],
            'put' => ['update', 'account'],
            'delete' => ['delete', 'account'],
        ];
    }

    protected function resetpass() : Response
    {
        $service = $this->getService();
        $data_reset = $this->getJsonData('email');
        $user = $service->where('email', $data_reset)->orWhere('phone', $data_reset)->first();
        if ($user) {
            try {
                $new_pass = $service->randomPassword();
                $user->password = $new_pass;
                $user->save();

                $this->get(BernardService::class)->sendMessage('api', 'queued_emails', [
                    'message' => [
                        'from' => 'none-reply@phuongnamdigital.com|phuongnamdigital',
                        'to' => sprintf("%s|%s", $user->email, $user->name),
                        'subject' => '[PHUONG NAM DIGITAL] - Reset Password',
                        'body' => "<div><p><strong>Mật khẩu mới của bạn: $new_pass</strong></p><p>Đây là email được gửi tự động, vui lòng không gửi phản hồi!</p></div>"
                    ],
                ]);

                return $this->json([
                    'status' => true,
                ]);
            } catch(Exception $e) {
                return $this->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            return $this->json([
                'status' => false,
                'message' => 'Email chưa được đăng ký!'
            ]);
        }
    }

    protected function change(): Response
    {
        $password = $this->getJsonData('password');
        $user = $this->getAuthed();
        $user->password = $password;

        return $this->json([
            'status' => $user->save(),
        ]);
    }

    protected function login(): Response
    {
        $service = $this->get(UserService::class);
        $result = $service->auth(
            $this->getJsonData('email'),
            $this->getJsonData('password'),
            $this->getJsonData('device_id')
        );
        if ($result->get('status')) {
            $user = $result->get('user');
            $token = $result->get('token');
            $user_data = [
                'id' => $user->getKey(),
                'email' => $user->email,
                'roles' => $user->getRoles(),
                'name' => $user->name,
                'area_id' => $user->area_id
            ];
            $data = [
                'status' => true,
                'token' => $token,
                'user' => $user_data,
                'first_login' => $result->get('first_login'),
            ];
            $statusCode = 200;
        } else {
            $data = [
                'status' => false,
            ];
            $statusCode = 401;
        }
        
        return $this->json($data, $statusCode);
    }

    public function logout(): Response
    {
        $device = $this->request->attributes->get('__device');

        return $this->json([
            'status' => $device->delete(),
        ]);
    }

    protected function commissionData(UserModel $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone,
            'name' => $user->name,
            'total_commission' => floatval($user->contracts->sum('commission')),
        ];
    }

    protected function commission(): Response
    {
        $from = $this->request->query->get('from');
        $to = $this->request->query->get('to');
        $f_date = DateTime::createFromFormat('Y-m-d', $from);
        $t_date = DateTime::createFromFormat('Y-m-d', $to);

        $users = $this->getService()->query()->with(['contracts' => function ($query) use ($f_date, $t_date) {
            if ($f_date instanceof DateTime) {
                $query->where('begin_date', '>=', $f_date);
            }
            if ($t_date instanceof DateTime) {
                $query->where('begin_date', '<=', $t_date);
            }
        }])->get();
        $result = [];
        foreach ($users as $user) {
            $result[] = $this->commissionData($user);
        }
        usort($result, function ($a, $b) {
            if ($a['total_commission'] == $b['total_commission']) {
                return 0;
            }
            return $a['total_commission'] < $b['total_commission'];
        });

        return $this->json([
            'status' => true,
            'data' => $result,
        ]);
    }

    public function me(): Response
    {
        $user = $this->getAuthed();

        return $this->json([
            'status' => true,
            'data' => $user
        ]);
    }
}
