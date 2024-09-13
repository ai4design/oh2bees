<div>
    <x-slot:title>
        Proxy Logs | Oh2Bees
    </x-slot>
    <x-server.navbar :server="$server" :parameters="$parameters" />
    <div class="flex gap-2">
        <x-server.sidebar :server="$server" :parameters="$parameters" />
        <div class="w-full">
            <h2 class="pb-4">Logs</h2>
            <livewire:project.shared.get-logs :server="$server" container="coolify-proxy" />
        </div>
    </div>
</div>
