<?php

namespace App\Http\Controllers\API;

use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;


class SmsTemplateController extends Controller
{
    public function index(Request $request)
    {
        $data = SmsTemplate::getSmsTemplate($request);
        $totalCount = SmsTemplate::countData();
        return ['datarows' => $data, 'count' => $totalCount];
    }

    public function show($id)
    {
        $smslTemplate = SmsTemplate::getOne($id);

        if ($smslTemplate->custom_content) {
            $response = [
                'smsSubject' => $smslTemplate->template_subject,
                'content' => $smslTemplate->custom_content,
                'isCustom' => true,
            ];
        } else {
            $response = [
                'smsSubject' => $smslTemplate->template_subject,
                'content' => $smslTemplate->default_content,
                'isCustom' => false,
            ];
        }

        return $response;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'subject' => 'required',
        ]);

        $success = SmsTemplate::updateData($id, [
            'template_subject' => $request->input('subject'),
            'custom_content' => $request->input('custom_content')
        ]);

        if (!$success) {
            return response()->json([
                'message' => Lang::get('lang.error_during_update')
            ], 404);
        }

        $response = [

            'message' => ucfirst(strtolower(Lang::get('lang.' . $request->template_name) . ' ' . Lang::get('lang.settings_saved_successfully')))
        ];

        return response()->json($response, 200);
    }
}
