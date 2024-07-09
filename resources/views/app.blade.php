<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- tailwind -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- momentjs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <!-- SweetAlert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        tr>td {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="max-w-7xl mx-auto p-6 lg:p-8">
        <div class="flex justify-center">
        </div>
        <div class="container mx-auto px-4">
            <h1 class="text-2xl font-bold mb-4">Lista de Ítems</h1>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">ID</th>
                        <th class="py-2 px-4 border-b">Código</th>
                        <th class="py-2 px-4 border-b">Monto</th>
                        <th class="py-2 px-4 border-b">Fecha</th>
                        <th class="py-2 px-4 border-b">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user['id'] }}</td>
                        <td>{{ $user['code'] }}</td>
                        <td>{{ number_format($user['amount'], 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($user['date'])->setTimezone('America/Santiago')->format('d-m-Y') }}</td>
                        <td><button class="bg-blue-500 text-white px-4 py-2" onclick="openModal({{ json_encode($user) }})">Editar</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div id="editModal" class="hidden fixed z-10 inset-0 overflow-y-auto flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-full md:w-3/4 lg:w-1/2">
            <div class="text-center">
                <h3 class="text-lg font-semibold mb-4">Editar Usuario</h3>
            </div>
            <form id="editForm" class="space-y-4">
                <input type="hidden" id="id" name="id">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Código</label>
                    <select id="code" name="code" class="bg-gray-50 border border-gray-300 text-gray-900 form-select mt-1 block w-full">
                    </select>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Monto</label>
                    <input type="text" id="amount" name="amount" class="bg-gray-50 border border-gray-300 text-gray-900 form-input mt-1 block w-full">
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" id="date" name="date" class="bg-gray-50 border border-gray-300 text-gray-900 form-input mt-1 block w-full">
                </div>
                <div class="text-center">
                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="sendUser()">Guardar</button>
                    <button type="button" class="ml-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500" onclick="closeModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        function openModal(user) {
            $.ajax({
                url: '/get-users',
                method: 'GET',
                success: function(data) {
                    $('#code').empty();
                    data.forEach(function(user) {
                        $('#code').append(`<option value="${user.code}">${user.name}</option>`);
                    });

                    $('#id').val(user.id);

                    let formattedAmount = new Intl.NumberFormat('es-CL').format(user.amount);
                    $('#amount').val(formattedAmount);

                    let formattedDate = moment(user.date).format('YYYY-MM-DD');
                    $('#date').val(formattedDate);

                    $('#editModal').removeClass('hidden');
                }
            });
        }

        function closeModal() {
            $('#editModal').addClass('hidden');
        }

        function sendUser() {
            let userId = $('#id').val();
            let userCode = $('#code option:selected').val();
            let userAmount = $('#amount').val().replace(/\./g, '');
            let userDate = moment($('#date').val()).toISOString();
            let userGithub = 'https://github.com/mephiuwu';

            if(!userId) return this.notification('error', 'No se encuentra el dato a modificar');
            if(!userCode) return this.notification('error', 'El código es necesario');
            if(!userAmount) return this.notification('error', 'El monto es necesario');
            if(!userDate) return this.notification('error', 'La fecha es necesaria');

            const userData = {
                id: userId,
                code: userCode,
                amount: parseInt(userAmount),
                date: userDate,
                github: userGithub
            };

            let self = this;

            $.ajax({
                url: '/send-user',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(userData),
                success: function(data) {
                    $('#editModal').addClass('hidden');
                    self.notification(data.status, data.message)
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function notification(status, message) {
            Swal.fire({
                position: 'bottom-end',
                icon: status,
                title: message,
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    popup: 'swal2-toast',
                    icon: 'swal2-icon-custom',
                    title: 'swal2-title-custom',
                },
            });
        }
    </script>
</body>

</html>