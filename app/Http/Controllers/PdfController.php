<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PDF;
use App\Traits\ErrorsTrait;
use Exception;

class PdfController extends Controller
{
    use ErrorsTrait;

    /**
     * @var userService
     */
    protected $userService;


    /**
     * UserController constructor
     *
     * @param App\Services\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Downloads a PDF User Report
     *
     * @param Illuminate\Http\Request $request
     */
    public function pdfDownload(Request $request)
    {
        try {
            $users = $this->userService->getAllUsers();
            $pdf = PDF::loadView('report', [
                "users" => $users,
                "total_tweets" => 0,
                "total_users" => count($users),
                "tweet_per_user" => 0
                ]);

            return $pdf->download('user_report.pdf');
        } catch (Exception $exception) {
            $errors = "Database error";
            if ($exception->getCode() !== 2002) {
                $errors = $exception->getMessage();
            }
            $result =  $this->set_status_and_error_message(Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
            
            return response()->json($result['response'], $result['status']);
        }
    }
}
