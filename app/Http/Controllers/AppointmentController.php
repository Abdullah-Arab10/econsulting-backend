<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function bookAppointment(Request $request)
    {

        $rules = [
            "clientId" => "required",
            "consultantId" => "required",
            "appointmentDate" => "requried|date",
            "appointmentStart" => "required|date_format:G:i:s",
            "appointmentEnd" => "required|date_format:G:i:s|after:appointmentStart"

        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $consultant = User::query()->join('consultants', 'users.id', '=', 'consultants.user_id')->where('users.id', '=', $request->consultantId)->get();
        $consultant = $consultant->toArray();
        $clientAppointments = Appointment::query()->where('client_id', '=', $request->clientId)->orWhere('consultant_id', '=', $request->clientId)->get();
        $clientAppointments = $clientAppointments->toArray();
        $consultantAppointments = Appointment::query()->where('consultant_id', '=', $request->consultantId)->get();
        $consultantAppointments = $consultantAppointments->toArray();
        $errorResponse = ["message" => "Consultant is not available!"];
        if (count($consultant) == 0) {
            return response()->json(["message" => "consultant is not found!"], 400);
        }
        $consultantInfo = $consultant[0];
        $appointmentStartRequest = new Carbon($request->appointmentStart);
        $appointmentEndRequest = Carbon::createFromFormat('G:i:s', $request->appointmentEnd);
        $shiftStart = $consultantInfo['shiftStart'];
        $shiftEnd = $consultantInfo['shiftEnd'];
        $appointmentDateRequest = Carbon::createFromDate($request->date);
        if ($appointmentStartRequest->lessThan($shiftStart) || $appointmentStartRequest->greaterThanOrEqualTo($shiftEnd)) {
            return response()->json([$errorResponse, "1"], 400);
        }
        foreach ($consultantAppointments as $appointment) {
            $appointmentDate = Carbon::createFromDate($appointment['appointment_date']);
            $appointmentStart = Carbon::createFromFormat('G:i:s', $appointment['appointment_start']);
            $appointmentEnd = Carbon::createFromFormat('G:i:s', $appointment['appointment_end']);
            if ($appointmentDate->eq($appointmentDateRequest)) {
                if ($appointmentStartRequest->greaterThanOrEqualTo($appointmentStart) && $appointmentStartRequest->lessThanOrEqualTo($appointmentEnd)) {
                    return response()->json([$errorResponse, '2'], 400);
                }
            }
        }
        foreach ($clientAppointments as $appointment) {
            $appointmentDate = Carbon::createFromDate($appointment['appointment_date']);
            $appointmentStart = Carbon::createFromFormat('G:i:s', $appointment['appointment_start']);
            $appointmentEnd = Carbon::createFromFormat('G:i:s', $appointment['appointment_end']);
            if ($appointmentDate->eq($appointmentDateRequest)) {
                if ($appointmentStartRequest->greaterThanOrEqualTo($appointmentStart) && $appointmentStartRequest->lessThanOrEqualTo($appointmentEnd)) {
                    return response()->json(["message" => "Sorry,you have another appointment in same time"], 400);
                }
            }
        }
        $appointment = Appointment::create([
            "client_id" => $request->clientId,
            "consultant_id" => $request->consultantId,
            "appointment_date" => $appointmentDateRequest,
            "appointment_start" => $appointmentStartRequest,
            "appointment_end" => $appointmentEndRequest
        ]);
        return response()->json([
            "message" => "appointment created successfully",
            "data" => $appointment
        ], 200);
    }
}
