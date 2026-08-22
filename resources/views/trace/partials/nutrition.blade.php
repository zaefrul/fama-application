<table class="w-full text-left text-sm">
    <tbody>
        @foreach ($nutrition as $row)
            <tr class="border-t border-border">
                <td class="px-4 py-1.5">{{ $row['name'] }}</td>
                <td class="font-medium">{{ $row['amount'] }}</td>
                <td class="pr-4 text-muted">{{ $row['dailyPercent'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
