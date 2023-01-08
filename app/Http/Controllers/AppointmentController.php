<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class AppointmentController extends Controller
{
    public function bookAppointment(Request $request)
    {

        $rules = [
            'clientId' => 'required',
            'consultantId' => 'required',
            'date' => 'required|date',
            'appointmentStart' => 'required|date_format:H:i',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $consultant = User::query()->join('consultants', 'users.id', '=', 'consultants.user_id')->where('users.id', '=', $request->consultantId)->first();
        $clientAppointments = Appointment::query()->where('client_id', '=', $request->clientId)->orWhere('consultant_id', '=', $request->clientId)->get();
        $clientAppointments = $clientAppointments->toArray();
        $consultantAppointments = Appointment::query()->where('consultant_id', '=', $request->consultantId)->get();
        $consultantAppointments = $consultantAppointments->toArray();
        $errorResponse = ["message" => "Consultant is not available!"];
        if (!$consultant) {
            return response()->json(["message" => "consultant is not found!", "errorId" => 0], 400);
        }
        $user = User::query()->where('id', '=', $request->clientId)->first();
        if (!$user) {
            return response()->json(["message" => "User is not found!", "errorId" => 1], 400);
        }
        $appointmentStartRequest = new Carbon($request->appointmentStart);
        $appointmentEndRequest = Carbon::createFromFormat('H:i', $request->appointmentStart)->addHour();
        $shiftStart = $consultant['shiftStart'];
        $shiftEnd = $consultant['shiftEnd'];
        $appointmentDateRequest = Carbon::createFromDate($request->date);
        if ($appointmentStartRequest->lessThan($shiftStart) || $appointmentStartRequest->greaterThanOrEqualTo($shiftEnd)) {
            return response()->json(["message" => "Consultant is not available!", "errorId" => 2], 400);
        }
        foreach ($clientAppointments as $appointment) {
            $appointmentDate = Carbon::createFromDate($appointment['appointment_date']);
            $appointmentStart = Carbon::createFromFormat('G:i:s', $appointment['appointment_start']);
            $appointmentEnd = Carbon::createFromFormat('G:i:s', $appointment['appointment_end']);
            if ($appointmentDate->eq($appointmentDateRequest)) {
                if ($appointmentStartRequest->greaterThanOrEqualTo($appointmentStart) && $appointmentStartRequest->lessThan($appointmentEnd)) {
                    return response()->json(["message" => "Sorry,you have another appointment in same time", "errorId" => 3], 400);
                }
            }
        }
        foreach ($consultantAppointments as $appointment) {
            $appointmentDate = Carbon::createFromDate($appointment['appointment_date']);
            $appointmentStart = Carbon::createFromFormat('G:i:s', $appointment['appointment_start']);
            $appointmentEnd = Carbon::createFromFormat('G:i:s', $appointment['appointment_end']);
            if ($appointmentDate->eq($appointmentDateRequest)) {
                if ($appointmentStartRequest->greaterThanOrEqualTo($appointmentStart) && $appointmentStartRequest->lessThan($appointmentEnd)) {
                    return response()->json(["message" => "Consultant is not available!", "errorId" => 2], 400);
                }
            }
        }
        if ($consultant['appointment_cost'] > $user['wallet'] && !$request->isVacation) {
            return response()->json(["message" => "Sorry,you don't have enough cash", "errorId" => 4], 400);
        }
        $user->wallet = $user->wallet - $consultant['appointment_cost'];
        $user->save();
        $consultant->wallet = $consultant->wallet + $consultant['appointment_cost'];
        $consultant->save();

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
    public function createVacations(Request $request)
    {
        $rules = [
            'consultantId' => 'required',
            'date' => 'required|date',
            'vacationStart' => 'required|date_format:H:i',
            'vacationEnd' => 'required|date_format:H:i',
            'repeat' => 'required|integer'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $consultant = User::query()->join('consultants', 'users.id', '=', 'consultants.user_id')->where('users.id', '=', $request->consultantId)->first();
        $consultantAppointments = Appointment::query()->where('consultant_id', '=', $request->consultantId)->get();
        $consultantAppointments = $consultantAppointments->toArray();
        if (!$consultant) {
            return response()->json(["message" => "consultant is not found!", "errorId" => 0], 400);
        }
        $vacationStartRequest = Carbon::createFromFormat('H:i', $request->vacationStart);
        $vacationEndRequest = Carbon::createFromFormat('H:i', $request->vacationEnd);
        $vacationDateRequest = Carbon::createFromDate($request->date);
        $shiftStart = $consultant['shiftStart'];
        $shiftEnd = $consultant['shiftEnd'];
        if ($vacationStartRequest->lessThan($shiftStart) || $vacationEndRequest->greaterThanOrEqualTo($shiftEnd)) {
            return response()->json(["message" => "please add vacation in your work shifts !", "errorId" => 1], 400);
        }
        $vacations = [];
        foreach ($consultantAppointments as $appointment) {
            $appointmentDate = Carbon::createFromDate($appointment['appointment_date']);
            $appointmentStart = Carbon::createFromFormat('G:i:s', $appointment['appointment_start']);
            $appointmentEnd = Carbon::createFromFormat('G:i:s', $appointment['appointment_end']);
            if ($appointmentDate->eq($vacationDateRequest)) {
                if ($vacationStartRequest->greaterThanOrEqualTo($appointmentStart) && $vacationStartRequest->lessThan($appointmentEnd)) {
                    if ($appointment['client_id'] == $appointment['consultant_id']) {
                        return response()->json(["message" => "Sorry , you have upcoming vacation in the same time!", "errorId" => 2], 400);
                    } else {
                        return response()->json(["message" => "Sorry , you have upcoming appointments!", "errorId" => 3], 400);
                    }
                }
            }
        }
        for ($i = 0; $i <= $request->repeat; $i++) {
            $vacation = Appointment::create([
                "client_id" => $request->consultantId,
                "consultant_id" => $request->consultantId,
                "appointment_date" => $vacationDateRequest->addDays($i * 7),
                "appointment_start" => $vacationStartRequest,
                "appointment_end" => $vacationEndRequest
            ]);
            array_push($vacations, $vacation);
        }
        return response()->json(["data" => $vacations, "message" => "vacations added successfully"], 200);
    }
    public function getAppointments($id)
    {
        $appointments = Appointment::query()->where('consultant_id', '=', $id)->get()->toArray();
        $response = [];
        foreach ($appointments as $appointment) {
            $key = $appointment['appointment_date'];
            $consultantDayAppointmentsInfo = Appointment::query()->where('consultant_id', '=', $id)->where('appointment_date', '=', $key)->select('appointment_start', 'appointment_end', 'client_id')->get()->toArray();

            for ($i = 0; $i < count($consultantDayAppointmentsInfo); $i++) {
                $appointmentInfo = $consultantDayAppointmentsInfo[$i];
                $clientsInfo = User::query()->where('id', '=', $appointmentInfo['client_id'])->select('first_name', 'last_name', 'image')->get()->toArray();
                $consultantDayAppointmentsInfo[$i] = array_merge($consultantDayAppointmentsInfo[$i], $clientsInfo[0]);
            }
            $response[$key] = $consultantDayAppointmentsInfo;
        }
        return response()->json($response);
    }
}
