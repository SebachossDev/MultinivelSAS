<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold">{{ $this->heading }}</h3>
                <select wire:model="filter" class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="departments">Departamentos</option>
                    <option value="cities">Ciudades</option>
                </select>
            </div>
        
            <div>
                {!! $this->chart->container() !!}
            </div>
        </div>
        
        @push('scripts')
            {!! $this->chart->script() !!}
        @endpush
    </x-filament::section>
</x-filament-widgets::widget>
