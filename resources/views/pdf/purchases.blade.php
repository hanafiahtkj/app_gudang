<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Mengatur font menjadi Calibri */
        body {
            font-family: Calibri, sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
        }

        .agenda th, .agenda td {
            vertical-align: top;
            padding: 2px 6px; /* Sesuaikan dengan ukuran padding yang diinginkan */
            font-weight: bold;
        }

        .content th, .content td {
            vertical-align: top;
            padding: 8px; /* Sesuaikan dengan ukuran padding yang diinginkan */
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center; font-size: 18px;">JACK KOLAM</h1>
    <h1 style="text-align: center; font-size: 14px; margin-bottom: 30px;">DAFTAR PEMASUKAN</h1>

    <table class="agenda" style="margin-bottom: 15px;">
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($tanggal)->isoFormat('DD MMMM YYYY') }}</td>
        </tr>
    </table>

    <table class="content" border="1" width="100%" style="border-color:#333">
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA PRODUK</th>
                <th>JUMLAH</th>
                <th>TOTAL HARGA</th>
            </tr>
        </thead>
        <tbody>
            @php
                $idx = 0;
            @endphp

            @foreach ($data as $rep)
                <tr>
                    <td align="center">{{ $idx = $idx + 1 }}</td>
                    <td>{{ $rep->name }}</td>
                    <td align="center">{{ $rep->total_quantity }}</td>
                    <th align="right">{{ number_format((float)$rep->total_unitcost, 2, '.', ',') }}</th>
                </tr>
            @endforeach

            <tr style="background-color: #999">
                <td align="center" colspan="2">TOTAL</td>
                <td align="center">{{ $data->sum('total_quantity') }}</td>
                <th align="right">{{ number_format((float)$data->sum('total_unitcost'), 2, '.', ',') }}</th>
            </tr>
        </tbody>
    </table>

</body>
</html>
