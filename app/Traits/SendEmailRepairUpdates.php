<?php 

namespace App\Traits;

use App\Models\RmaTickets;

trait SendEmailRepairUpdates
{
    private function sendNotifiedRequesterEmail($ticket_id, $callback) {
        $rma_ticket = RmaTickets::find($ticket_id);

        if ($rma_ticket->notify == 1) {
            return $callback();
        }
    }
}