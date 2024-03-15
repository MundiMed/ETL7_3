<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use DB;
use Log;
ini_set('memory_limit', '-1');
set_time_limit(0);
 
class ImportController extends Controller
{
    public function truncateTables(){
       // echo "aqui";
        ///*
        try {
            DB::connection('mysql_mundimed_v1')->select('call sp_truncate_all_tables()');
            echo "Limpeza de tabelas XDB realizada com sucesso";
            Log::debug('Limpeza de tabelas XDB realizada com sucesso');

        } catch (\Throwable $th) {
            Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
            Log::error("Erro ao truncar: " . $th->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);

        }
        //*/

    }

    public function index()
    {
        $hi = new \DateTime();
        $hi = $hi->format('Y-m-d H:i:s');

        try {
            //code...
            $tables = DB::connection('mysql_mundimed_v1')->select('SHOW TABLES LIKE "xdb%"');
            $tables = array_map('current', $tables);
           
            $qtd = intval(env('SYSTEMS_QUANTITY'));
           
            for($i = 1; $i<=$qtd;$i++){
    
                $now = new \DateTime();
                $now = $now->format('Y-m-d H:i:s');    
               // Log::debug('Iniciado sistema '.$i.' em '.$now);
                
                foreach ($tables as $key => $tab) {
                    $db = substr($tab,0,8);
                    $tb = substr_replace($tab, "", 0, 8);
                   // Log::debug('Iniciado sistema '. $i.' na tabela '.$db.$tb);
                    switch ($db) {
                        case 'xdb_ace_': $this->conn_acervo($db, $tb, $i); break;
                        case 'xdb_cre_': $this->conn_credenciados($db, $tb, $i); break;
                        case 'xdb_med_': $this->conn_medicamentos($db, $tb, $i); break;
                        case 'xdb_pre_': $this->conn_precos($db, $tb, $i); break;
                        default: break;
                    }
                }
            }        
    
            $hf = new \DateTime();
            $hf = $hf->format('Y-m-d H:i:s');
         
            Log::debug('Iniciado em '.$hi.' e conluído em '.$hf);
        } catch (\Throwable $th) {
            Log::error("Erro ao identificar todas as tabelas: " . $th->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);
        }
     
    }

    public function conn_acervo($db, $tb, $systemId){
        
        $field = '';
        
        switch ($tb) {
            case 'acervo_imovel': $field = 'moa_sequencia';break; 
            case 'cad_geral_acervo': $field = 'cad_geral_cad_codigo';break;
            case 'mov_orcamento_acervo': $field = 'mov_orcamento_os_orc_codigo';break;
            case 'mov_os_acervo': $field = 'mov_os_os_codigo';break;
            case 'mov_os_check_list_acervo': $field = 'mov_os_check_list_chk_seq';break;
            case 'mov_pedido_acervo': $field = 'mov_pedido_id';break;
            default:break;
        }       
        try {
            $value = DB::connection('mysql_mundimed_v1')->table($db.$tb)->select($field)->where('system_id', $systemId)->orderBy($field, 'desc')->limit(1)->first();
        } catch (\Throwable $th) {
            Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);
        
        }
        
        //if($value?->$field === null){ // só faz sentido para php 8.1
        if(is_null($value) || $value->$field === null){
            $val = 0;
        }else{
            $val = $value->$field;
        }       
        
        $dados = DB::connection('mysql_acervo_'.$systemId)->table($tb);
        
        if($val > 0){
            $dados = $dados->where($field,'>',$val);
        }
        
        
        try {
            $dados = $dados->orderBy($field, 'asc')->get();
            
        } catch (\Throwable $th) {
            Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
            Log::error("Erro na " . $db.$tb);
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);      
        }
        
        $inserts = $dados->map(function ($item) use ($systemId) {
            $arrayItem = (array) $item;
            $arrayItem['system_id'] = $systemId;
            return $arrayItem;
        })->toArray();        
        
        $parts = array_chunk($inserts, 1000);
        
        foreach ($parts as $part) {
            
            try {
                DB::connection('mysql_mundimed_v1')->table($db.$tb)->insert($part);
            } catch (\Throwable $th) {
                Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                Log::error("Erro na " . $db.$tb." -> ". $part);
                return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);                  }
        }
    }

    public function conn_credenciados($db, $tb, $systemId){

        if($systemId == 1){
            
            $systemId = 0;
            $field = '';
            
            switch ($tb) {
                case 'cad_geral': $field = 'cad_codigo';break; 
                case 'cad_geral_especializacao': $field = 'cad_geral_cad_codigo';break;
                case 'cad_geral_mural': $field = 'pob_data_emissao';break;
                case 'cad_geral_polos': $field = 'pol_codigo';break;
                case 'cad_geral_polos_cidades': $field = 'pol_sequencia';break;
                case 'cad_geral_prontuario': $field = 'cad_geral_cad_codigo';break;
                case 'cad_segmento': $field = 'id_segmento';break;
                case 'cad_segmento_credenciado': $field = 'id';break;
                case 'fin_banco': $field = 'id';break;
                case 'fin_conta_banco': $field = 'id';break;
                case 'med_fornecedores': $field = 'id';break;
                case 'med_laboratorio': $field = 'id';break;
                case 'med_laboratorios': $field = 'id';break;
                case 'med_laboratorios_old': $field = 'id';break;
                case 'sis_parametro': $field = 'id_parametro';break;
                case 'sis_parametro_contexto': $field = 'id';break;
                case 'sis_parametro_tipo': $field = 'id';break;
                case 'sis_parametros_sistema': $field = 'par_id';break;
                case 'tab_cidades': $field = 'cid_codigo';break;
                case 'tab_especializacao': $field = 'id';break;
                case 'tab_ramo_atuacao': $field = 'tab_tipo_cadastro_tic_codigo';break; //?
                case 'tab_sistemas': $field = 'id';break;
                case 'tab_tipo_cadastro': $field = 'tic_codigo';break;
                default:break;
            }   

            try {
                $value = DB::connection('mysql_mundimed_v1')->table($db.$tb)->select($field)->where('system_id', $systemId)->orderBy($field, 'desc')->limit(1)->first();
            } catch (\Throwable $th) {
                Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                Log::error("Erro na " . $db.$tb);
                return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);      
            }
            
            //if($value?->$field === null){ // só faz sentido para php 8.1
            if(is_null($value) || $value->$field === null){
                $val = 0;
            }else{
                $val = $value->$field;
            }       
            
            $dados = DB::connection('mysql_mundimed_credenciados')->table($tb);
            
            
            if($val > 0){
                $dados = $dados->where($field,'>',$val);
            }            
            
            try {
                $dados = $dados->orderBy($field, 'asc')->get();
                
            } catch (\Throwable $th) {
                Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                Log::error("Erro na " . $db.$tb);
                return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);      
            }
            $inserts = $dados->map(function ($item) use ($systemId) {
                $arrayItem = (array) $item;
                $arrayItem['system_id'] = $systemId;
                return $arrayItem;
            })->toArray();        
            
            $parts = array_chunk($inserts, 1000);
            
            foreach ($parts as $part) {
                try {
                    DB::connection('mysql_mundimed_v1')->table($db.$tb)->insert($part);
                } catch (\Throwable $th) {
                    Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                    Log::error("Erro na " . $db.$tb." -> ". $part);
                    return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);                
                }
            }
        }
    }

    public function conn_medicamentos($db, $tb, $systemId){

        $field = '';
        
        switch ($tb) {
            case 'cad_geral': $field = 'cad_codigo';break; 
            case 'cad_geral_especializacao': $field = 'cad_geral_cad_codigo';break; 
            case 'cad_geral_orgao_empenho': $field = 'emp_sequencia';break; 
            case 'cad_geral_orgao_empenho_mov': $field = 'mve_movimento';break; 
            case 'cad_segmento': $field = 'id_segmento';break; 
            case 'cad_segmento_credenciado': $field = 'id';break; 
            case 'cad_usuario': $field = 'cad_geral_cad_codigo';break; 
            case 'cad_usuario_acesso': $field = 'cad_usuario_cad_geral_cad_codigo';break; 
            case 'cardapio_alimentos': $field = 'id';break; 
            case 'cardapio_alimentos_contrato': $field = 'id';break; 
            case 'cardapio_alimentos_precos': $field = 'cardapio_alimentos_id';break; 
            case 'cargos': $field = 'id';break; 
            case 'estoque_classe': $field = 'estoque_classe_id';break; 
            case 'estoque_grupo': $field = 'estoque_grupo_id';break; 
            case 'estoque_itens': $field = 'id';break; 
            case 'estoque_movimento': $field = 'id';break; 
            case 'estoque_movimento_itens': $field = 'mvt_seq';break; 
            case 'estoque_requisicao': $field = 'id';break; 
            case 'estoque_saldo': $field = 'cad_geral_cad_codigo';break; 
            case 'farmacia_receita': $field = 'id';break; 
            case 'farmacia_receita_tipo': $field = 'id';break; 
            case 'fin_banco': $field = 'id';break; 
            case 'fin_conta_banco': $field = 'id';break; 
            case 'fin_mov_os_nf': $field = 'id';break; 
            case 'imovel': $field = 'id';break; 
            case 'imovel_tipo': $field = 'id';break; 
            case 'integracao_orcamentos': $field = 'NumeroDoOrcamento';break; 
            case 'integracao_os': $field = 'NumeroDoOrcamento';break; 
            case 'integracao_os_relatorio': $field = 'NumeroDoOrcamento';break; 
            case 'med_laboratorio': $field = 'id';break; 
            case 'med_produtos': $field = 'id';break; 
            case 'med_referencia': $field = 'id';break; 
            case 'mov_avalicao_os': $field = 'id';break; 
            case 'mov_orcamento_os': $field = 'orc_codigo';break; 
            case 'mov_orcamento_os_itens': $field = 'mov_orcamento_os_orc_codigo';break; 
            case 'mov_os': $field = 'os_codigo';break; 
            case 'mov_os_check_list': $field = 'mov_orcamento_os_orc_codigo';break; 
            case 'mov_os_endereco': $field = 'id_endereco';break; 
            case 'mov_os_obs': $field = 'pob_sequencia';break; 
            case 'mov_os_orcamentistas': $field = 'os_data_cad';break; 
            case 'mov_os_status': $field = 'id';break; 
            case 'sc_log': $field = 'id';break; 
            case 'sis_filial': $field = 'fil_codigo';break; 
            case 'sis_nivel_acesso': $field = 'niv_codigo';break; //?
            case 'sis_perfil': $field = 'sis_filial_fil_codigo';break; //?
            case 'sis_sistema': $field = 'sis_codigo';break;
            case 'tab_descontos': $field = 'id';break;
            case 'tab_insumos_reduzida': $field = 'id';break;
            case 'tab_sinapi_classe': $field = 'cla_sigla';break;////?
            case 'tab_tipo_item': $field = 'tip_item';break;
            default:break;
        } 

        $isValid = true;

        if($systemId == 4 || $systemId == 9 || $systemId == 14 || $systemId == 15 || $systemId == 19 || $systemId == 22){
            if($tb == 'cardapio_alimentos' || $tb == 'cargos' || $tb =='med_laboratorio' || $tb == 'mov_avalicao_os' || $tb=='tab_tipo_item'){
                $isValid = false;
            }
        }else if($systemId == 12 || $systemId == 20){
            if($tb == 'cardapio_alimentos' || $tb == 'cargos' || $tb =='med_laboratorio' || $tb == 'mov_avalicao_os' || $tb == 'mov_os_endereco' || $tb=='tab_tipo_item'){
                $isValid = false;
            }
        }else if($systemId == 21){
            if($tb == 'cardapio_alimentos' || $tb=='tab_tipo_item'){
                $isValid = false;
            }
        }
        
        if($isValid){


            try {
                $value = DB::connection('mysql_mundimed_v1')->table($db.$tb)->select($field)->where('system_id', $systemId)->orderBy($field, 'desc')->limit(1)->first();
            } catch (\Throwable $th) {
                Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                Log::error("Erro na " . $db.$tb);
                return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);      
            }

            //if($value?->$field === null){ só faz sentido para o php 8.1
            if(is_null($value) || $value->$field === null){
                $val = 0;
            }else{
                $val = $value->$field;
            }       
            
            $dados = DB::connection('mysql_medicamentos_'.$systemId)->table($tb);
            
            if($val > 0){
                $dados = $dados->where($field,'>',$val);
            }        
            
            try {
                $dados = $dados->orderBy($field, 'asc')->get();
            } catch (\Throwable $th) {
                Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                Log::error("Erro na " . $db.$tb);
                return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);       
            }
    
            $inserts = $dados->map(function ($item) use ($systemId) {
                $arrayItem = (array) $item;
                $arrayItem['system_id'] = $systemId;
                return $arrayItem;
            })->toArray();        
            
            $parts = array_chunk($inserts, 1000);
            
            foreach ($parts as $part) {
                
                try {
                    DB::connection('mysql_mundimed_v1')->table($db.$tb)->insert($part);
                } catch (\Throwable $th) {
                    Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                    Log::error("Erro na " . $db.$tb." -> ".$part);
                    return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);                      }
            }
        }
    }

    public function conn_precos($db, $tb, $systemId){
        if($systemId == 1 || $systemId == 17){
            if($systemId == 1){
                $systemId = 0;
            }
            $field = '';
            
            switch ($tb) {
                case 'cardapio_alimentos': $field = 'id';break; 
                case 'cardapio_alimentos_contrato': $field = 'id';break; 
                case 'cardapio_alimentos_precos': $field = 'cardapio_alimentos_contrato_id';break; 
                case 'estoque_classe': $field = 'estoque_classe_id';break; 
                case 'estoque_grupo': $field = 'estoque_grupo_id';break; //?
                case 'estoque_itens': $field = 'id';break; 
                case 'med_produtos': $field = 'id';break; 
                case 'med_referencia': $field = 'id';break; 
                case 'tab_insumos_reduzida': $field = 'id';break; 
                case 'tab_sinapi_classe': $field = 'cla_sigla';break; //?
                case 'tab_tipo_item': $field = 'tip_item';break; //?
                
                default:break;
            }   
            
            
            try {
                $value = DB::connection('mysql_mundimed_v1')->table($db.$tb)->select($field)->where('system_id', $systemId)->orderBy($field, 'desc')->limit(1)->first();
            } catch (\Throwable $th) {
                Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                Log::error("Erro na " . $db.$tb);
                return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);      
            }
            //if($value?->$field === null){ só faz sentido para o php8.1
            if(is_null($value) || $value->$field === null){
                $val = 0;
            }else{
                $val = $value->$field;
            }       
            
            $dados = DB::connection('mysql_mundimed_precos')->table($tb);
            
            if($val > 0){
                $dados = $dados->where($field,'>',$val);
            }
            
            
            try {
                $dados = $dados->orderBy($field, 'asc')->get();
            } catch (\Throwable $th) {
                Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                Log::error("Erro na " . $db.$tb);
                return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);                  
            }
            
            $inserts = $dados->map(function ($item) use ($systemId) {
                $arrayItem = (array) $item;
                $arrayItem['system_id'] = $systemId;
                return $arrayItem;
            })->toArray();        
            $parts = array_chunk($inserts, 1000);
            foreach ($parts as $part) {
                
                try {
                    DB::connection('mysql_mundimed_v1')->table($db.$tb)->insert($part);
                } catch (\Throwable $th) {
                    Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                    Log::error("Erro na " . $db.$tb." -> ". $part);
                    return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);      
                }
            }
        }
    }
}
