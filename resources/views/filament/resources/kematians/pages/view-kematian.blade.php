<x-filament-panels::page>
    <div class="w-full flex justify-center py-4">
        <div id="surat-wrapper" style="width: 100%; display: flex; justify-content: center;">
            <div id="surat-container" style="transform-origin: top center;">
                @include('filament.resources.kematians.pages._kematian-content', [
                    'record' => $record
                ])
            </div>
        </div>
    </div>

    <script>
        function scaleSurat() {
            const container = document.getElementById('surat-container');
            const wrapper = document.getElementById('surat-wrapper');
            const parentWidth = wrapper.offsetWidth;
            const suratWidthPx = 794; // 210mm in pixels at 96dpi
            const suratHeightPx = 1123; // 297mm in pixels at 96dpi

            // Calculate scale to fit container width (with padding)
            // Min scale 0.5 (50%) untuk mobile, max 1 (100%) untuk desktop
            const scale = Math.max(0.5, Math.min(1, (parentWidth - 32) / suratWidthPx));

            container.style.transform = `scale(${scale})`;

            // Adjust wrapper height to match scaled content (prevent empty space below)
            wrapper.style.height = `${suratHeightPx * scale}px`;
        }

        // Scale on load and resize
        window.addEventListener('load', scaleSurat);
        window.addEventListener('resize', scaleSurat);
    </script>
</x-filament-panels::page>
