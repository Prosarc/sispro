<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Mail\ContartoMail;
use App\Contrato;
use App\User;

class SendMailContrato extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contrato:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia mails de contaratos a punto de vencer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $User = User::where('UsRol', 'Tesorería')->Orwhere('UsRol2', 'Tesorería')->get();
        $Users = User::select('email')->where('UsRol', 'Tesorería')->get();
        $Users2 = User::select('email')->where('UsRol2', 'Tesorería')->get();
        $Contratos = DB::table('contratos')
            ->join('clientes', 'clientes.ID_Cli', 'contratos.Fk_ContraCli')
            ->select('clientes.CliShortname', 'contratos.*')
            ->where('ContraNotifiVigencia', date("Y-n-j"))
            ->get();

        foreach($Users as $User){
            if(isset($Contratos[0]->ContraNotifiVigencia)){
                Mail::to($User->email)->send(new ContartoMail($Contratos));
            }
        }
        foreach($Users2 as $User2){
            if(isset($Contratos[0]->ContraNotifiVigencia)){
                Mail::to($User2->email)->send(new ContartoMail($Contratos));
            }
        }
    }
}
