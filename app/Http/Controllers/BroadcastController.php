<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BroadcastController extends Controller
{
    public function getBroadcast(Request $request)
    {
        if ($request->ajax()) {
            $data = Message::select('id', 'message', 'status')->get();
            return Datatables::of($data)->addIndexColumn()
                ->setRowClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('broadcast');
    }
    public function addBroadcast(Request $req)
    {
        $req->validate([
            'file' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048',
            'message' => 'required'
        ]);

        if ($req->file()) {
            $fileName = time() . '_' . $req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');

            $row = 0;
            $skip_row_number = array("1");
            $csv_file = fopen(storage_path('app/public/') . $filePath, "r");

            while (($data = fgetcsv($csv_file, 1000, ",")) !== FALSE) {
                $row++;
                if (in_array($row, $skip_row_number)) {
                    continue;
                } else {
                    $req->message = str_replace("[Var1]", $data[1], $req->message);
                    $req->message = str_replace("[Var2]", $data[2], $req->message);
                    $req->message = str_replace("[Var3]", $data[3], $req->message);
                    $req->message = str_replace("[Var4]", $data[4], $req->message);
                    $req->message = str_replace("[Var5]", $data[5], $req->message);
                    $req->message = str_replace("[Var6]", $data[6], $req->message);
                    $req->message = str_replace("[Var7]", $data[7], $req->message);
                    $req->message = str_replace("[Var8]", $data[8], $req->message);
                    $req->message = str_replace("[Var9]", $data[9], $req->message);
                    $req->message = str_replace("[Var10]", $data[10], $req->message);

                    $message = new Message([
                        'target' => $data[0],
                        'message' => $req->message,
                        'status' => 'pending',
                    ]);
                    $message->save();
                }
            }

            return back()
                ->with('success', 'File has been uploaded.')
                ->with('file', $fileName);
        }
    }
}
