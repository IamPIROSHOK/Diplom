<?php

namespace App\Console\Commands;

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Discount;
use App\Models\User;
use Carbon\Carbon;
use App\Facades\NotificationFacade;

class SendNewDiscountNotifications extends Command
{
    protected $signature = 'send:new-discount-notifications';

    protected $description = 'Send new discount notifications to users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $discounts = Discount::where('created_at', '>=', Carbon::now()->subDay())->get();

        foreach ($discounts as $discount) {
            $users = User::all();
            foreach ($users as $user) {
                NotificationFacade::sendNotification($user,
                    "Новая акция: {$discount->title} - {$discount->percentage}% off",
                    'App\Notifications\NewDiscountNotification');
            }
        }

        $this->info('Уведовмление отправлено успешно!');
    }
}

