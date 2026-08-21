<?php

namespace App\Domain;

enum Role: string
{
    case Exporter = 'EXPORTER';
    case FamaOfficer = 'FAMA_OFFICER';
}
