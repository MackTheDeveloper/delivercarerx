<?php

namespace App\Repository;

use App\Models\EmailTemplates;
use App\Models\EmailTemplatesCc;
use App\Models\PasswordReset;
use App\Models\User;
use Mail;

class EmailTemplatesRepository
{
    /**
     * Send email
     * @return bool
     */
    public static function sendMail($slug, $data)
    {
        $template = self::getTemplate($slug);
        if ($template) {
            $mailBody = $template->body;
            $variables = [];
            $values = [];
            foreach ($data as $key => $value) {
                $variables[] = "{" . $key . "}";
                $values[] = $value;
            }
            $mailBody = str_replace($variables, $values, $mailBody);
            $ccEmails = [];
            $ccList = EmailTemplatesCc::selectRaw('email_cc')->where('template_id', $template->id)->get()->toArray();

            foreach ($ccList as $key => $value) {
                $ccEmails[] = $value['email_cc'];
            }
            try {
                Mail::send([], [], function ($message) use ($mailBody, $template, $data, $ccEmails) {
                    $message->to($data['EMAIL'])
                        // ->cc($ccEmails)
                        ->subject($template->subject)
                        ->setBody($mailBody, 'text/html'); // for HTML rich messages
                });
            } catch (\Exception $e) {
                dd($e);
            }
            return 1;
        }
    }

    public static function getTemplate($slug)
    {
        $return = EmailTemplates::where('slug', $slug)->first();
        return ($return) ?: [];
    }


    public function create($data)
    {
        return EmailTemplates::create($data);
    }

    public function fetch($id)
    {
        return EmailTemplates::where('id', $id)->first();
    }

    public function update($data, $id = null)
    {
        return EmailTemplates::where('id', $id)->update($data);
    }
    public function delete($id)
    {
        return EmailTemplates::where('id', $id)->delete();
    }
}
