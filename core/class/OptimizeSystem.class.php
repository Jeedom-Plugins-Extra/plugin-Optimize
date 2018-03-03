<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

class OptimizeSystem
{
    private $systemLogs = array(
        'scenario'   => 'Scenario',
        'plugin'     => 'Plugin',
        'market'     => 'Market',
        'api'        => 'Api',
        'connection' => 'Connection',
        'interact'   => 'Interact',
        'tts'        => 'TTS',
        'report'     => 'Report'
    );

    /**
     * Evalue les informations d'un log système.
     *
     * @param array $informations Informations à évaluer
     *
     * @return array Rapport sur les informations évaluées
     */
    private function rateSystemLogInformations($informations)
    {
        $rating = array();

        // Valeurs par défaut
        $rating['score'] = 0;
        $rating['log'] = 'ok';

        // Les logs doivent être désactivés
        if ($informations['log'] === true)
        {
            $rating['score']++;
            $rating['log'] = 'warn';
        }

        return $rating;
    }

    /**
     * Obtenir les informations et une évaluation de l'ensemble des logs système.
     *
     * @return array Informations sur l'ensemble des scénarios
     */
    public function getInformations()
    {
        $informations = array();

        foreach ($this->systemLogs as $systemLogId => $systemLogName)
        {
            $systemLogInformations = array();
            $systemLogInformations['id'] = $systemLogId;
            $systemLogInformations['name'] = $systemLogName;
            $systemLogConfig = config::byKey('log::level::' . $systemLogId);
            $systemLogInformations['log'] = false;
            // Chaque type de log est stocké dans un tableau et identifié par un nombre sauf "default"
            // 1000 représente "Aucun"
            foreach ($systemLogConfig as $logType => $value)
            {
                if ($value == 1 && $logType != 1000)
                {
                    $systemLogInformations['log'] = true;
                }
            }
            $rating = $this->rateSystemLogInformations($systemLogInformations);
            $systemLogInformations['rating'] = $rating;
            array_push($informations, $systemLogInformations);
        }
        return $informations;
    }

    /**
     * Désactiver les logs d'un service.
     *
     * @param integer $systemLogId Identifiant du scénario
     */
    public function disableLogs($systemLogId)
    {
        $systemLogConfig = config::byKey('log::level::' . $systemLogId);
        foreach ($systemLogConfig as $key => $value)
        {
            if ($value != 0)
            {
                $systemLogConfig[$key] = 0;
            }
        }
        $systemLogConfig[1000] = 1;
        config::save('log::level::' . $systemLogId, $systemLogConfig);
    }
}
