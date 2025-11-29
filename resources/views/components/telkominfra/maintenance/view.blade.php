@props([
    'perjalanans',
    'search' => '',
    'searchMode',
    'totalPerjalanan',
    'perjalananSelesai',
    'perjalananBelumSelesai',
])

<div class="bg-white shadow-xl sm:rounded-lg p-2 sm:p-4">
    <h3 class="text-lg font-semibold mb-4">Daftar Sesi Drive Test</h3>

    @if (Auth::user()?->admin)
        {{-- ========== FORM UPLOAD DATA BARU ========== --}}
        <div class="mb-6 border p-4 rounded-lg bg-gray-50">
            <h4 class="text-md font-bold mb-3">Unggah Data Perjalanan (NMF) Baru</h4>
            <form action="{{ route('perjalanan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <input type="hidden" name="id_perjalanan" value="">

                    <div>
                        <label for="nmf_file" class="block text-sm font-medium text-gray-700">
                            File Nemo (.nmf atau .txt log):
                        </label>
                        <input type="file" id="nmf_file" name="nmf_file[]" accept=".nmf,.txt" multiple
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white p-2">
                    </div>

                    <div>
                        <label for="nama_pengguna" class="block text-sm font-medium text-gray-700">Nama
                            Pengguna:</label>
                        <input type="text" id="nama_pengguna" name="nama_pengguna" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border"
                            value="{{ Auth::user()->name ?? 'User Default' }}">
                    </div>

                    <div>
                        <label for="nama_tempat" class="block text-sm font-medium text-gray-700">Nama Tempat /
                            Lokasi:</label>
                        <input type="text" id="nama_tempat" name="nama_tempat" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border" value="Palembang">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status Pengujian:</label>
                        <select id="status" name="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Before" selected>Before</option>
                            <option value="After">After</option>
                        </select>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <strong>Error!</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300 transition ease-in-out duration-150 mt-2">
                    Unggah Data Baru
                </button>
            </form>
        </div>
    @endif

    <div class="container mx-auto p-0 sm:p-4">

        <div class="container mx-auto p-0 sm:p-4">

            {{-- ============================== STATISTIK RINGKASAN PERJALANAN (BARU) ============================== --}}
            <h2 class="text-2xl font-extrabold text-gray-900 mb-4 border-b pb-2">Dashboard Data Maintenance</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

                {{-- Total Keseluruhan --}}
                <div
                    class="bg-indigo-100 p-6 rounded-xl shadow-lg border-l-4 border-indigo-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Total Perjalanan</p>
                        <p class="text-4xl font-extrabold text-indigo-900">{{ number_format($totalPerjalanan ?? 0) }}
                        </p>
                    </div>
                    <i class="fas fa-route text-5xl text-indigo-400 opacity-30 mt-1 ml-4"></i>
                </div>

                {{-- Sudah Selesai --}}
                <div
                    class="bg-green-100 p-6 rounded-xl shadow-lg border-l-4 border-green-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-green-600 uppercase tracking-wider">Sudah Selesai</p>
                        <p class="text-4xl font-extrabold text-green-900">{{ number_format($perjalananSelesai ?? 0) }}
                        </p>
                    </div>
                    <i class="fas fa-check-circle text-5xl text-green-400 opacity-30 mt-1 ml-4"></i>
                </div>

                {{-- Belum Selesai --}}
                <div
                    class="bg-yellow-100 p-6 rounded-xl shadow-lg border-l-4 border-yellow-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-yellow-600 uppercase tracking-wider">Belum Selesai</p>
                        <p class="text-4xl font-extrabold text-yellow-900">
                            {{ number_format($perjalananBelumSelesai ?? 0) }}</p>
                    </div>
                    <i class="fas fa-hourglass-half text-5xl text-yellow-400 opacity-30 mt-1 ml-4"></i>
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            @livewire('perjalanan-table')

        </div>
    </div>
</div>
