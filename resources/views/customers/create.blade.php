@extends('layouts.app')

@section('content')
<div class="page-title my-3">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h5></h5>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/customers">Khách Hàng</a></li>
                    <li class="breadcrumb-item">Thêm</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

</div>


<div class="card">

    <div class="card-body">
        <div class="">
            <h5 style="margin-bottom: 10px;">Thêm Khách Hàng</h5>
            <form method="POST" action="{{ route('customers.store') }}" class="row" id="multi-customer-form">
                @csrf
                <div id="customer-fields-container">
                    @php
                    $oldCustomerCount = count(old('company_name', [0 => '']));
                    @endphp
                    @for ($i = 0; $i < $oldCustomerCount; $i++) <div class="customer-fields row"
                        id="customer-fields-{{ $i }}">
                        <h5 style="padding-top: 15px;"></h5>
                        <div class="form-group col-lg-4">
                            <label for="company_name_{{ $i }}">Tên Công Ty</label>
                            <input type="text" class="form-control @error('company_name.' . $i) is-invalid @enderror"
                                name="company_name[]" id="company_name_{{ $i }}" list="company-list-{{ $i }}"
                                value="{{ old('company_name.' . $i) }}" placeholder="Chọn hoặc nhập tên công ty">
                            <datalist id="company-list-{{ $i }}">
                                @foreach ($companies as $company)
                                <option value="{{ $company->company_name }}">
                                    @endforeach
                            </datalist>
                            @error('company_name.' . $i)
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-lg-4">
                            <label for="address_{{ $i }}">Địa Chỉ</label>
                            <input type="text" class="form-control @error('address.' . $i) is-invalid @enderror"
                                name="address[]" id="address_{{ $i }}" placeholder="Địa chỉ"
                                value="{{ old('address.' . $i) }}">
                            @error('address.' . $i)
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="customer_name_{{ $i }}">Họ Và Tên Khách Hàng</label>
                            <input type="text" class="form-control @error('customer_name.' . $i) is-invalid @enderror"
                                name="customer_name[]" id="customer_name_{{ $i }}" placeholder="Họ Và Tên Khách Hàng"
                                value="{{ old('customer_name.' . $i) }}">
                            @error('customer_name.' . $i)
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="phone_{{ $i }}">Điện Thoại</label>
                            <input type="text" class="form-control @error('phone.' . $i) is-invalid @enderror"
                                name="phone[]" id="phone_{{ $i }}" placeholder="Điện thoại"
                                value="{{ old('phone.' . $i) }}">
                            @error('phone.' . $i)
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="email_{{ $i }}">Email</label>
                            <input type="text" class="form-control @error('email.' . $i) is-invalid @enderror"
                                name="email[]" id="email_{{ $i }}" placeholder="Email" value="{{ old('email.' . $i) }}">
                            @error('email.' . $i)
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="password_{{ $i }}">Mật Khẩu</label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control @error('password.' . $i) is-invalid @enderror" name="password[]"
                                    id="password_{{ $i }}" placeholder="Mật Khẩu" value="{{ old('password.' . $i) }}">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" data-target="password_{{ $i }}"
                                        style="cursor: pointer;">
                                        <i class="fas fa-eye" id="eye_icon_{{ $i }}"></i>
                                    </span>
                                </div>
                                @error('password.' . $i)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                document.querySelectorAll('.toggle-password').forEach(function (element) {
                                    element.addEventListener('click', function () {
                                        const targetId = this.getAttribute('data-target');
                                        const passwordField = document.getElementById(targetId);
                                        const icon = this.querySelector('i');

                                        if (passwordField.type === "password") {
                                            passwordField.type = "text";
                                            icon.classList.remove("fa-eye");
                                            icon.classList.add("fa-eye-slash");
                                        } else {
                                            passwordField.type = "password";
                                            icon.classList.remove("fa-eye-slash");
                                            icon.classList.add("fa-eye");
                                        }
                                    });
                                });
                            });
                        </script> --}}
                        <div class="form-group col-lg-12">
                            <label for="customer_type_{{ $i }}">Loại Khách Hàng</label>
                            <div class="customer-type-box row">
                                @foreach ($customerTypes as $type)
                                <div class="col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="customer_type[{{ $i }}][]"
                                            id="type_{{ $i }}_{{ $type->id }}" value="{{ $type->id }}" {{
                                            in_array($type->id, old('customer_type.' . $i, [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_{{ $i }}_{{ $type->id }}">
                                            {{ $type->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('customer_type.' . $i)
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="description_{{ $i }}">Mô Tả</label>
                            <textarea name="description[]" id="description_{{ $i }}" cols="20" rows="3"
                                class="form-control">{{ old('description.' . $i) }}</textarea>
                            @error('description.' . $i)
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        @if ($i > 0)
                        <div class="form-group col-lg-12">
                            <button type="button" class="btn btn-danger btn-sm remove-customer"
                                data-index="{{ $i }}">Xóa Khách Hàng</button>
                        </div>
                        @endif
                        <hr>
                </div>
                @endfor
        </div>
        <div class="col-sm-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-success me-1">Tạo Khách Hàng</button>
            <button type="button" class="btn btn-primary" id="add-customer">Thêm Khách Hàng</button>
        </div>
        </form>
        <script>
            $('.select2').select2({
                placeholder: "Chọn hoặc nhập tên công ty",
                allowClear: true,
                tags: true
            });
            const customerTypes = @json($customerTypes);
            let customerIndex = document.querySelectorAll('.customer-fields').length;
            function createCustomerFields(index, companyName) {
                const container = document.createElement('div');
                container.classList.add('customer-fields', 'row');
                container.id = `customer-fields-${index}`;
                let customerTypeHtml = '';
                customerTypes.forEach(type => {
                    customerTypeHtml += `
                        <div class="col-lg-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="customer_type[${index}][]"
                                    id="type_${index}_${type.id}" value="${type.id}">
                                <label class="form-check-label" for="type_${index}_${type.id}">
                                    ${type.name}
                                </label>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = `
                    <h5 style="padding-top: 15px;">Thông Tin Khách Hàng </h5>
                    <div class="form-group col-lg-4">
                        <label for="company_name_${index}">Tên Công Ty</label>
                        <input type="text" class="form-control" name="company_name[]"
                            id="company_name_${index}" placeholder="Tên công ty" value="${companyName}" readonly>
                        <div class="text-danger" id="company_name_${index}_error"></div>
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="address_${index}">Địa Chỉ</label>
                        <input type="text" class="form-control" name="address[]"
                            id="address_${index}" placeholder="Địa chỉ">
                        <div class="text-danger" id="address_${index}_error"></div>
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="customer_name_${index}">Họ Và Tên Khách Hàng</label>
                        <input type="text" class="form-control" name="customer_name[]"
                            id="customer_name_${index}" placeholder="Họ Và Tên Khách Hàng">
                        <div class="text-danger" id="customer_name_${index}_error"></div>
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="phone_${index}">Điện Thoại</label>
                        <input type="text" class="form-control" name="phone[]"
                            id="phone_${index}" placeholder="Điện thoại">
                        <div class="text-danger" id="phone_${index}_error"></div>
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="email_${index}">Email</label>
                        <input type="text" class="form-control" name="email[]"
                            id="email_${index}" placeholder="Email">
                        <div class="text-danger" id="email_${index}_error"></div>
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="password_${index}">Mật Khẩu</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password[]"
                                id="password_${index}" placeholder="Mật Khẩu">
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password" data-target="password_${index}" style="cursor: pointer;">
                                    <i class="fas fa-eye" id="eye_icon_${index}"></i>
                                </span>
                            </div>
                        </div>
                        <div class="text-danger" id="password_${index}_error"></div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="customer_type_${index}">Loại Khách Hàng</label>
                        <div class="customer-type-box row">
                            ${customerTypeHtml}
                        </div>
                        <div class="text-danger" id="customer_type_${index}_error"></div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label for="description_${index}">Mô Tả</label>
                        <textarea name="description[]" id="description_${index}" cols="20" rows="3" class="form-control"></textarea>
                        <div class="text-danger" id="description_${index}_error"></div>
                    </div>
                    <div class="form-group col-lg-12">
                        <button type="button" class="btn btn-danger btn-sm remove-customer" data-index="${index}">Xóa Khách Hàng</button>
                    </div>
                    <hr>
                `;
                return container;
            }
            // Gắn sự kiện toggle ẩn/hiện mật khẩu bằng ủy quyền sự kiện
            document.addEventListener('click', function (e) {
                if (e.target.closest('.toggle-password')) {
                    const toggleBtn = e.target.closest('.toggle-password');
                    const inputId = toggleBtn.getAttribute('data-target');
                    const input = document.getElementById(inputId);
                    const icon = toggleBtn.querySelector('i');

                    if (input && icon) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    }
                }
            });


            document.getElementById('add-customer').addEventListener('click', function () {
                const companyName = document.querySelector('[name="company_name[]"]').value;

                if (!companyName) {
                    alert("Vui lòng nhập Tên Công Ty khi thêm khách hàng!");
                    return;
                }
                const customerFieldsContainer = document.getElementById('customer-fields-container');
                customerFieldsContainer.appendChild(createCustomerFields(customerIndex, companyName));
                customerIndex++;
            });
            document.getElementById('customer-fields-container').addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-customer')) {
                    const index = e.target.getAttribute('data-index');
                    document.getElementById(`customer-fields-${index}`).remove();
                }
            });
            document.getElementById('multi-customer-form').addEventListener('submit', function (e) {
                let isValid = true;
                let emailSet = new Set();
                document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');
                document.querySelectorAll('[name="email[]"]').forEach((input, index) => {
                    const email = input.value;

                    if (!email) {
                        return; 
                    }
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        isValid = false;
                        document.getElementById(`email_${index}_error`).textContent = 'Email không hợp lệ';
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                    if (emailSet.has(email)) {
                        isValid = false;
                        document.getElementById(`email_${index}_error`).textContent = 'Email đã tồn tại trong danh sách';
                        input.classList.add('is-invalid');
                    } else {
                        emailSet.add(email);
                    }
                });
                document.querySelectorAll('[name="phone[]"]').forEach((input, index) => {
                    const phone = input.value;
                    if (phone) {
                        const phoneRegex = /^[0-9]{10,11}$/;
                        if (!phoneRegex.test(phone)) {
                            isValid = false;
                            document.getElementById(`phone_${index}_error`).textContent = 'Số điện thoại phải có 10-11 chữ số';
                            input.classList.add('is-invalid');
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    }
                });
                document.querySelectorAll('.customer-type-box').forEach((box, index) => {
                    const checkboxes = box.querySelectorAll('input[type="checkbox"]:checked');
                    if (checkboxes.length === 0) {
                        isValid = false;
                        document.getElementById(`customer_type_${index}_error`).textContent = 'Vui lòng chọn ít nhất một loại khách hàng';
                    }
                });
                if (!isValid) {
                    e.preventDefault();
                    alert('Vui lòng kiểm tra lại các trường thông tin!');
                }
            });
        </script>
    </div>
</div>
</div>
<style>
    .select2-container--default .select2-selection--single {
        height: 39px;
    }

    .input-group-text {
        padding: 0.575rem 0.75rem;
    }
</style>

@endsection