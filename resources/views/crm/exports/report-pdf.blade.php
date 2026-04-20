<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
        }

        .header {
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0 0 6px;
            font-size: 20px;
            color: #114a8f;
        }

        .meta {
            color: #64748b;
            font-size: 10px;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #dbe5f0;
            padding: 8px 7px;
            vertical-align: top;
        }

        th {
            background: #eff6ff;
            color: #0f172a;
            font-size: 10px;
            text-transform: uppercase;
        }

        tr:nth-child(even) td {
            background: #f8fafc;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="meta">Generated at: {{ now()->format('d M Y h:i A') }}</div>
        @if (!empty($appliedFilters))
            <div class="meta">Filters: {{ $appliedFilters }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
