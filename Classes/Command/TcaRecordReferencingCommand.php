<?php


namespace DFAU\ToujouApi\Command;


interface TcaRecordReferencingCommand
{

    public function getUid(): string;

    public function getTableName(): string;
}
