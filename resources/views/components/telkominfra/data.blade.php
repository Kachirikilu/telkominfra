@props(['perjalanans'])

<div class="bg-white shadow-xl sm:rounded-lg">

    <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Daftar Sesi Drive Test</h3>

        {{-- Form Unggah Data --}}
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
                        <input type="file" id="nmf_file" name="nmf_file[]" accept=".nmf,.txt" multiple required
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



        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

            <!-- Area Search Form -->
            <div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">
                <form action="{{ route('telkominfra.index') }}" method="GET"
                    class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari ID, ID Perjalanan, Lokasi, atau Pengguna..."
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">

                    <div class="flex space-x-3 w-full sm:w-auto">
                        <button type="submit"
                            class="w-1/2 sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md">
                            <i class="fas fa-search"></i> Cari
                        </button>

                        {{-- Tombol Reset Pencarian --}}
                        @if (isset($search) && $search)
                            <a href="{{ route('telkominfra.index') }}"
                                class="w-1/2 sm:w-auto flex items-center justify-center py-2 px-4 text-gray-600 hover:text-gray-900 bg-gray-100 rounded-lg transition duration-150 shadow-sm border border-gray-300">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Data Perjalanan -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Peta</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID Perjalanan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lokasi</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pengguna</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Waktu Unggah</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($perjalanans as $perjalanan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $perjalanan->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        {{-- Tombol Lihat Detail/Peta --}}
                                        <button
                                            onclick="window.location='{{ route('telkominfra.show', $perjalanan->id) }}'"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300 transition ease-in-out duration-150">
                                            Lihat Peta
                                        </button>


                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $perjalanan->id_perjalanan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $perjalanan->nama_tempat }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $perjalanan->nama_pengguna }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $perjalanan->created_at->format('d M Y H:i:s') }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        {{-- Form Hapus --}}
                                        <form action="{{ route('perjalanan.destroy', $perjalanan->id) }}"
                                            method="POST" class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perjalanan ini? Data log yang terkait juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition ease-in-out duration-150">Hapus</button>
                                        </form>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500" colspan="5">
                                        Tidak ada sesi Drive Test yang ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Link pagination (Pastikan Anda telah memasukkan $search ke appends() di Controller) --}}
                    <div class="p-4">
                        {{ $perjalanans->links() }}
                    </div>

                </div>
            </div>
        </div>


    </div>
</div>
