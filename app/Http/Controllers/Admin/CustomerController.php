<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CustomerCreatedMail;
use App\Models\ActionHistory;
use App\Models\Customer;
use App\Models\CustomerType;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("customers.index");
    }

    public function getData(Request $request)
    {
        $customers = Customer::with('customerTypes')->get();

        // dd($customers);

        return DataTables::of($customers)
            ->addColumn('check', function ($row) {
                return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
            })
            ->addColumn('customer_type', function ($row) {
                return $row->customerTypes->pluck('name')->implode(', ');
            })
            ->addIndexColumn()
            ->addColumn('actions', function ($row) {
                $action = '
                    <div class="d-flex gap-1">
                        <a href="' . route('customers.edit', $row->id) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <!--<a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                            <i class="fas fa-trash-alt"></i>
                        </a>-->
                    </div>
                    <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
                                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->farm_name ?? 'N/A') . '</span>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <a href="/farms/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
                return $action;
            })
            ->addColumn('actions1', function ($row) {
                return '
                    <a href="' . route('customers.edit', $row->id) . '" class="edit-btn"><i class="fa-solid fa-pencil"></i></a>
                    <!-- <form action="' . route('customers.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="delete-btn text-danger" style="border:none;background: none;" onclick="return confirm(\'Bạn có chắc chắn muốn xóa?\')"><i class="fa-sharp fa-regular fa-trash"></i></button>
                    </form> -->
                ';
            })
            ->rawColumns(['check', 'customer_type', 'actions'])
            ->make(true);
    }
    // public function deleteMultiple(Request $request)
    // {
    //     $request->validate([
    //         'ids' => 'required|array',
    //         'ids.*' => 'integer',
    //     ]);
    //     Customer::whereIn('id', $request->ids)->delete();
    //     return response()->json([
    //         'message' => 'Xóa thành công.',
    //         'deleted_ids' => $request->ids
    //     ]);
    // }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customerTypes = CustomerType::all();
        $companies = Customer::all();
        return view('customers.create', compact('customerTypes', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     try {
    //         $data = $request->validate([
    //             'customer_name' => 'required',
    //             'password' => 'required',
    //             'company_name' => 'required',
    //             'customer_type' => 'required',
    //             'phone' => 'required',
    //             'email' => 'required',
    //             'address' => 'required',
    //             'description' => '',
    //         ], [
    //             'customer_name.required' => 'Họ và tên khách hàng là bắt buộc.',
    //             'password.required' => 'Mật khẩu là bắt buộc.',
    //             'company_name.required' => 'Tên công ty là bắt buộc.',
    //             'customer_type.required' => 'Loại khách hàng là bắt buộc.',
    //             'phone.required' => 'Số điện thoại là bắt buộc.',
    //             'email.required' => 'Email là bắt buộc.',
    //             'email.email' => 'Email phải có định dạng hợp lệ.',
    //             'address.required' => 'Địa chỉ là bắt buộc.',
    //             'description.required' => 'Mô tả là bắt buộc.',
    //         ]);

    //         if (User::where('email', $data['email'])->exists()) {
    //             return redirect()->back()->withInput()->with('error', 'Email đã tồn tại trong hệ thống!');
    //         }

    //         $user = User::create([
    //             'name' => $data['customer_name'],
    //             'email' => $data['email'],
    //             'password' => $data['password'],
    //         ]);

    //         $user->assignRole('Khách Hàng');

    //         Customer::create([
    //             'company_name' => $data['company_name'],
    //             'customer_type' => $data['customer_type'],
    //             'phone' => $data['phone'],
    //             'email' => $data['email'],
    //             'address' => $data['address'],
    //             'description' => $data['description'] ?? null,
    //         ]);

    //         // dd($data);

    //         // Customer::create($data);

    //         return redirect()->route('customers.index')->with('message', 'Khách hàng đã được tạo.');
    //     } catch (\Throwable $th) {
    //         // return redirect()->back()->with('error', 'Tạo khách hàng thất bại!');
    //         return redirect()->back()
    //             ->withErrors(['error' => $th->getMessage()])
    //             ->withInput();
    //     }
    // }

    public function store(Request $request)
    {
        try {
            // Validate dữ liệu
            $data = $request->validate([
                'customer_name' => 'required|array',
                'customer_name.*' => 'required|string',
                'company_name' => 'required|array',
                'company_name.*' => 'required|string',
                'customer_type' => 'required|array',
                'customer_type.*' => 'exists:customer_types,id',
                'phone' => 'required|array',
                'phone.*' => 'required|string',
                'email' => 'required|array',
                'email.*' => 'required|email',
                'address' => 'required|array',
                'address.*' => 'required|string',
                'description' => 'nullable|array',
                'description.*' => 'nullable|string',
                'password' => 'required|array',
                'password.*' => 'required|string',
            ], [
                'customer_name.required' => 'Tên khách hàng không được để trống.',
                'customer_name.*.required' => 'Vui lòng nhập tên khách hàng đầy đủ cho các trường.',
                'customer_name.*.string' => 'Tên khách hàng phải là một chuỗi ký tự.',

                'company_name.required' => 'Tên công ty không được để trống.',
                'company_name.*.required' => 'Vui lòng nhập tên công ty đầy đủ cho các trường.',
                'company_name.*.string' => 'Tên công ty phải là một chuỗi ký tự.',

                'customer_type.required' => 'Vui lòng chọn loại khách hàng.',
                'customer_type.*.exists' => 'Loại khách hàng không hợp lệ.',

                'phone.required' => 'Số điện thoại không được để trống.',
                'phone.*.required' => 'Vui lòng nhập số điện thoại đầy đủ cho các trường.',
                'phone.*.string' => 'Số điện thoại phải là một chuỗi ký tự.',

                'email.required' => 'Email không được để trống.',
                'email.*.required' => 'Vui lòng nhập email đầy đủ cho các trường.',
                'email.*.email' => 'Vui lòng nhập địa chỉ email hợp lệ.',

                'address.required' => 'Địa chỉ không được để trống.',
                'address.*.required' => 'Vui lòng nhập địa chỉ đầy đủ cho các trường.',
                'address.*.string' => 'Địa chỉ phải là một chuỗi ký tự.',

                'description.*.string' => 'Mô tả phải là một chuỗi ký tự.',

                'password.required' => 'Mật khẩu không được để trống.',
                'password.*.required' => 'Vui lòng nhập mật khẩu đầy đủ cho các trường.',
                'password.*.string' => 'Mật khẩu phải là một chuỗi ký tự.',
            ]);

            $customers = [];
            foreach ($data['email'] as $index => $email) {
                if (User::where('email', $email)->exists()) {
                    return redirect()->back()->withInput()->with('error', 'Email đã tồn tại trong hệ thống!');
                }
            }
            foreach ($data['customer_name'] as $index => $customer_name) {
                $customer = Customer::create([
                    'company_name' => $data['company_name'][$index],
                    'phone' => $data['phone'][$index],
                    'email' => $data['email'][$index],
                    'address' => $data['address'][$index],
                    'description' => $data['description'][$index] ?? null,
                ]);
                $customers[] = $customer;
            }

            foreach ($customers as $index => $customer) {
                // if (User::where('email', $data['email'][$index])->exists()) {
                //     return redirect()->back()->withInput()->with('error', 'Email đã tồn tại trong hệ thống!');
                // }

                $password = isset($data['password'][$index]) && !empty($data['password'][$index])
                    ? $data['password'][$index]
                    : 'default_password';

                $user = User::create([
                    'name' => $data['customer_name'][$index],
                    'email' => $data['email'][$index],
                    'password' => bcrypt($password),
                ]);

                $user->assignRole('Khách Hàng');

                $customer->user_id = $user->id;
                $customer->save();
                $customer->customerTypes()->attach($data['customer_type'][$index]);
                Mail::to($user->email)->send(new CustomerCreatedMail($data['customer_name'][$index], $user->email, $password, $data['company_name'][$index]));

                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Tạo',
                    'model_type' => 'Khách Hàng',
                    'details' => "Đã tạo khách hàng: " . $user->name . " (Email: " . $user->email . ", Công ty: " . $data['company_name'][$index] . ")",
                ]);
            }

            return redirect()->route('customers.index')->with('message', 'Thông tin khách hàng đã được tạo.');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['error' => $th->getMessage()])->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     $customer = Customer::findOrFail($id);
    //     $user = User::where('email', $customer->email)->first([
    //         'id',
    //         'name',
    //         'email',
    //         // Không cần lấy password nếu không dùng
    //     ]);
    //     return view('customers.edit', compact('customer', 'user'));
    // }

    public function edit(string $id)
    {
        $customer = Customer::with('users:id,name,email', 'customerTypes')->findOrFail($id);
        // dd($customer);
        $allCustomerTypes = CustomerType::all();

        return view('customers.edit', [
            'customer' => $customer,
            'user' => $customer->users,
            'allCustomerTypes' => $allCustomerTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     try {
    //         $data = $request->validate([
    //             'company_name' => 'required',
    //             'customer_type' => 'required',
    //             'phone' => 'required',
    //             'email' => 'required',
    //             'address' => 'required',
    //             'description' => '',
    //         ]);

    //         // dd($data);

    //         $customer = Customer::findOrFail($id);
    //         $customer->update($data);

    //         return redirect()->route('customers.index')->with('success', 'Khách hàng đã được cập nhật!');
    //     } catch (\Throwable $th) {
    //         return redirect()->back()->with('error', 'Cập nhật thất bại kiểm tra lại thông tin!');
    //     }
    // }

    // public function update(Request $request, string $id)
    // {
    //     try {
    //         $data = $request->validate([
    //             'customer_name' => 'required',
    //             'password' => 'required',
    //             'company_name' => 'required',
    //             'customer_type' => 'required',
    //             'phone' => 'required',
    //             'email' => 'required',
    //             'address' => 'required',
    //             'description' => '',
    //         ]);

    //         // dd($data);
    //         $existingCustomer = Customer::findOrFail($id);
    //         $oldEmail = $existingCustomer->email;

    //         if ($oldEmail !== $data['email']) {
    //             $emailExists = User::where('email', $data['email'])->exists();
    //             if ($emailExists) {
    //                 return redirect()->back()->with('error', 'Email này đã được sử dụng bởi tài khoản khác!');
    //             }
    //         }

    //         $existingCustomer->update([
    //             'company_name' => $data['company_name'],
    //             'customer_type' => $data['customer_type'],
    //             'phone' => $data['phone'],
    //             'email' => $data['email'],
    //             'address' => $data['address'],
    //             'description' => $data['description'] ?? null,
    //         ]);
    //         $user = User::where('email', $oldEmail)->first();
    //         if ($user) {
    //             $user->update([
    //                 'name' => $data['customer_name'],
    //                 'email' => $data['email'],
    //                 'password' => Hash::make($request->input('password')), // nếu bạn muốn cập nhật password
    //             ]);
    //             if ($request->filled('password')) {
    //                 $user->update([
    //                     'password' => Hash::make($request->input('password')),
    //                 ]);
    //             }
    //         }


    //         $existingCustomer->update($data);

    //         return redirect()->route('customers.index')->with('message', 'Khách hàng đã được cập nhật!');
    //     } catch (\Throwable $th) {
    //         return redirect()->back()->with('error', 'Cập nhật thất bại kiểm tra lại thông tin!');
    //     }
    // }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'customer_name' => 'required',
                'password' => 'nullable',
                'company_name' => 'required',
                // 'customer_type' => 'required',
                'customer_type' => 'required|array',
                'customer_type.*' => 'exists:customer_types,id',
                'phone' => 'required',
                'email' => 'required|email',
                'address' => 'required',
                'description' => 'nullable',
            ]);

            $existingCustomer = Customer::findOrFail($id);
            $oldEmail = $existingCustomer->email;

            if ($oldEmail !== $data['email']) {
                $emailExists = User::where('email', $data['email'])->where('id', '!=', $existingCustomer->user_id)->exists();
                if ($emailExists) {
                    return redirect()->back()->with('error', 'Email này đã được sử dụng bởi tài khoản khác!');
                }
            }

            $existingCustomer->update([
                'company_name' => $data['company_name'],
                // 'customer_type' => $data['customer_type'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'description' => $data['description'] ?? null,
            ]);

            $user = User::find($existingCustomer->user_id);

            if ($user) {
                $updateData = [
                    'name' => $data['customer_name'],
                    'email' => $data['email'],
                ];

                if ($request->filled('password')) {
                    $updateData['password'] = Hash::make($request->input('password'));
                }

                $user->update($updateData);
            }
            $existingCustomer->customerTypes()->sync($data['customer_type']);
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Cập nhật',
                'model_type' => 'Khách Hàng',
                'details' => "Đã cập nhật khách hàng: " . $data['customer_name'] . "\n" .
                    "Công ty: " . $data['company_name'] . "\n" .
                    "Số điện thoại: " . $data['phone'] . "\n" .
                    "Email: " . $data['email'] . "\n" .
                    "Địa chỉ: " . $data['address'],
            ]);
            return redirect()->route('customers.index')->with('message', 'Khách hàng đã được cập nhật!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Cập nhật thất bại, kiểm tra lại thông tin!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {

    //     $customer = Customer::findOrFail($id);

    //     if ($customer) {
    //         $customer->delete();
    //         return redirect()->route('customers.index')->with('success', 'Khách hàng đã được xóa!');
    //     } else {
    //         return redirect()->route('customers.index')->with('message', 'Xóa khách hàng thất bại.');
    //     }
    // }
    public function deleteMultiple(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer',
            ]);

            $customers = Customer::whereIn('id', $request->ids)->get();
            $messages = [];
            $allDeleted = true;

            foreach ($customers as $customer) {
                if ($customer->contracts()->count() > 0) {
                    $messages[] = "Khách hàng '{$customer->company_name}' đã có hợp đồng, bạn không được phép xóa khách hàng này.";
                    $allDeleted = false;
                    continue;
                }
                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Xóa',
                    'model_type' => 'Khách Hàng',
                    'details' => "Đã xóa khách hàng: '{$customer->company_name}' (Email: {$customer->email})",
                ]);
                $customer->users?->delete();
                $customer->delete();
                $messages[] = "Khách hàng '{$customer->company_name}' đã được xóa.";
            }
            if (!$allDeleted) {
                return response()->json([
                    'message' => implode('<br>', $messages),
                    'error' => true
                ], 400);
            }
            return response()->json([
                'message' => 'Xóa thành công các khách hàng đã chọn.',
                'error' => false,
                'deleted_ids' => $request->ids
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xóa dữ liệu: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }
    public function destroy(string $id)
    {

        $customer = Customer::findOrFail($id);

        if ($customer) {
            $user = $customer->users;
            $customerName = $customer->customer_name;
            $customerEmail = $customer->email;
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Xóa',
                'model_type' => 'Khách Hàng',
                'details' => "Đã xóa khách hàng: $customerName (Email: $customerEmail)",
            ]);
            $user?->delete();
            $customer->delete();
            return redirect()->route('customers.index')->with('success', 'Khách hàng đã được xóa!');
        } else {
            return redirect()->route('customers.index')->with('message', 'Xóa khách hàng thất bại.');
        }
    }
}
