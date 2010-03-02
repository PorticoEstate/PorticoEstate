<?php

    class frontend_borental {

        /**
         *
         * @param integer $org_unit_id
         */
        public function get_units($org_unit_id)
        {
            /*
             * 1. hent alle kontraktsparter som har org unit id (foreløpig bruker vi result_unit_number i rentalparty)
             * 2. hent alle kontrakter på kontraktspartene
             * 3. hent alle leieobjekt på kontraktene
             * 4. hent ut bygg-ider, location_code, fra leieobjektet
             */

        }

    }
