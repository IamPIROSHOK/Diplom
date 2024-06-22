<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use App\Facades\NotificationFacade;

class SendAppointmentReminders extends Command
{
    protected $signature = 'send:appointment-reminders';

    protected $description = 'Send appointment reminders to users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $appointments = Appointment::where('appointment_date', Carbon::tomorrow()->toDateString())->get();

        foreach ($appointments as $appointment) {
            $user = User::find($appointment->user_id);
            if ($user) {
                NotificationFacade::sendNotification($user,
                    "Напоминание: у вас назначена встреча на завтра в {$appointment->start_time}",
                    'App\Notifications\AppointmentReminder');
            }
        }

        $this->info('Напоминание о записи отправлено успешно!');
    }
}

