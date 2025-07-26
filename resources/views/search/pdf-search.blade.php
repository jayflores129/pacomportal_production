<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    
    <table border="1">
        <thead>
            <tr>
                <th>RMA #</th>
                <th>Date Requested</th>
                <th>PO Number</th>
                <th>Status</th>
                <th>Currency</th>
                <th>Requester Name</th>
                <th>Requester Phone</th>
                <th>Requester Email</th>
                <th>Company Name</th>
                <th>Company Phone</th>
                <th>Company Country</th>
                <th>Company Address</th>
                <th style="text-align:center;">Total RMA Items</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rmaTickets as $rma)
                <tr>
                    <td>{{ "R$rma->id" }}</td>
                    <td>{{ "$rma->requested_date" }}</td>
                    <td>{{ "$rma->po_number" }}</td>
                    <td>{{ "$rma->status" }}</td>
                    <td>{{ "$rma->currency" }}</td>
                    <td>{{ "$rma->requester_name" }}</td>
                    <td>{{ "$rma->requester_phone" }}</td>
                    <td>{{ "$rma->requester_email" }}</td>
                    <td>{{ "$rma->company_name" }}</td>
                    <td>{{ "$rma->company_phone" }}</td>
                    <td>{{ "$rma->company_country" }}</td>
                    <td>{{ "$rma->company_address" }}</td>
                    <td style="text-align:center;">{{ $rma->faulty_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <style>
        @page { margin: 10px; }
        body { 
            margin: 10px; 
        }

        table {
            border-collapse: collapse;
        }

        th  {
            font-size: 13px;
            padding: 4px;
        }

        td  {
            font-size: 13px;
            padding: 4px;
        }
    </style>

</body>
</html>