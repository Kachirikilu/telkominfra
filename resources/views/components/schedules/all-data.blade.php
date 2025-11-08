<div class="max-w-[1080px] mx-auto flex-1 p-1 sm:p-3 md:p-6 lg:p-8 overflow-y-auto">
        
        <div class="scroll-mt-20 mx-2 md:mx-0 mt-6 mb-4">
        <h2 class="text-xl font-semibold mb-4 border-b border-gray-200 pb-2">Tester</h2>
        @if($iotCamera->isEmpty())
            <p class="text-gray-600 text-xs sm:text-sm text-center py-20 mt-3">Tidak ada jadwal untuk wedbj.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2 md:gap-4">
                @foreach($iotCamera as $iot)
                    <a href="/iot/show/{{ $iot->id_device }}" class="block bg-white rounded-md shadow-md overflow-hidden hover:scale-105 hover:shadow-lg  transition duration-300">
                        @if($iot->image)
                            <img src="{{ asset($iot->image) }}" alt="{{ $iot->image }}" class="w-full aspect-square object-cover">
                        @else
                            <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-500">Tidak Ada image</div>
                        @endif
                        <div class="p-3">
                            <h3 class="font-semibold sm:text-lg mb-2">Device ID: {{ $iot->id_device }}</h3>
                            <p class="text-gray-600 text-xs sm:text-sm">Pesan: {{ $iot->message }}</p>

                            <p class="text-gray-500 text-xs sm:text-sm">
                                Diambil pada: {{ \Carbon\Carbon::parse($iot->created_at)->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm:ss') }} WIB
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>