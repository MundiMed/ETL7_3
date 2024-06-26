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
        try {
            DB::connection('mysql_mundimed_v1')->select('call sp_truncate_all_tables()');
            echo "01 - Limpeza de tabelas XDB realizada com sucesso";
            Log::debug('01 - Limpeza de tabelas XDB realizada com sucesso');

        } catch (\Throwable $th) {
            Log::error("01 - Erro ao acessar o banco de dados: " . $th->getMessage());
            Log::error("01 - Erro ao truncar: " . $th->getMessage());
            return response()->json(['erro' => '01 - Ocorreu um erro na operação com o banco de dados.'], 500);
        }      
    }

    public function callSPInsertApprovedServiceOrderItens(){
        try {
            $hi = new \DateTime();
            $hi = $hi->format('Y-m-d H:i:s');
            DB::connection('mysql_mundimed_v1')->select('call sp_insert_approved_service_order_itens()');
            $hf = new \DateTime();
            $hf = $hf->format('Y-m-d H:i:s');
            Log::debug('02 - Iniciado em '.$hi.' e conluído em '.$hf);
            
            echo "03 - Ordens de Serviços (ITENS) Aprovadas, foram inseridas com sucesso";
            Log::debug('03 - Iniciado em '.$hi.' e conluído em '.$hf. ' - Ordens de Serviços (ITENS) Aprovadas, foram inseridas com sucesso');
        } catch (\Throwable $th) {
            Log::error("03 - Erro ao acessar o banco de dados: " . $th->getMessage());
            Log::error("03 - Erro ao inserir ordens de serviço aprovadas: " . $th->getMessage());
            return response()->json(['erro' => '03 - Ocorreu um erro na operação com o banco de dados.'], 500);
        }      
    }

    public function callSPInsertApprovedServiceOrders(){
        try {
            $hi = new \DateTime();
            $hi = $hi->format('Y-m-d H:i:s');
            DB::connection('mysql_mundimed_v1')->select('call sp_insert_approved_service_orders()');
            $hf = new \DateTime();
            $hf = $hf->format('Y-m-d H:i:s');
            
            echo "04 - Ordens de Serviços Aprovadas, foram inseridas com sucesso";
            Log::debug('04 - Iniciado em '.$hi.' e conluído em '.$hf. ' - Ordens de Serviços Aprovadas, foram inseridas com sucesso');
        } catch (\Throwable $th) {
            Log::error("04 - Erro ao acessar o banco de dados: " . $th->getMessage());
            Log::error("04 - Erro ao inserir ordens de serviço aprovadas: " . $th->getMessage());
            return response()->json(['erro' => '04 - Ocorreu um erro na operação com o banco de dados.'], 500);
        }      
    }

    public function callSPInsertAccreditedSuppliers(){
        try {
            $hi = new \DateTime();
            $hi = $hi->format('Y-m-d H:i:s');
            DB::connection('mysql_mundimed_v1')->select('call sp_insert_accredited_suppliers()');
            $hf = new \DateTime();
            $hf = $hf->format('Y-m-d H:i:s');
            echo "05 - Iniciado em '.$hi.' e conluído em '.$hf. ' - Cadastro de credenciados foi realizada com sucesso";
            Log::debug('05 - Iniciado em '.$hi.' e conluído em '.$hf. ' - Cadastro de credenciados foi realizada com sucesso');
        } catch (\Throwable $th) {
            Log::error("05 -  Erro ao acessar o banco de dados: " . $th->getMessage());
            Log::error("05 - Erro ao cadastrar novos credenciados: " . $th->getMessage());
            return response()->json(['erro' => '05 - Ocorreu um erro na operação com o banco de dados.'], 500);
        }      
    }

    public function callSPCreateApprovedOrders(){
        try {
           
            $hi = new \DateTime();
            $hi = $hi->format('Y-m-d H:i:s');
            DB::connection('mysql_mundimed_v1')->select('call sp_truncate_approved_orders()');
            $qtd = intval(env('SYSTEMS_QUANTITY'));
            for($i = 1; $i<=$qtd;$i++){    
                DB::connection('mysql_mundimed_v1')->select('call sp_create_approved_orders('.$i.')');
            }
            $hf = new \DateTime();
            $hf = $hf->format('Y-m-d H:i:s');
            DB::connection('mysql_mundimed_v1')->select('call sp_update_products_approved_orders()');
            echo "06 - Cadastro de ordens aprovadas foi realizada com sucesso";
            Log::debug('06 - Iniciado em '.$hi.' e conluído em '.$hf. '  - Cadastro de ordens aprovadas foi realizada com sucesso');

            
            $hi = new \DateTime();
            $hi = $hi->format('Y-m-d H:i:s');
            DB::connection('mysql_mundimed_v1')->select('call sp_create_approved_orders_losing_suppliers()');
            $hf = new \DateTime();
            $hf = $hf->format('Y-m-d H:i:s');
            echo "07 - Cadastro de ordens recusadas foi realizada com sucesso";
            Log::debug('07 - Iniciado em '.$hi.' e conluído em '.$hf. ' - Cadastro de ordens recusadas foi realizada com sucesso');
            $qtd = intval(env('SYSTEMS_QUANTITY'));

            $hi = new \DateTime();
            $hi = $hi->format('Y-m-d H:i:s');
            for($i = 1; $i<=$qtd;$i++){
                DB::connection('mysql_mundimed_v1')->select('call sp_update_approved_orders_losing_suppliers('.$i.')');
            }
            
            $hf = new \DateTime();
            $hf = $hf->format('Y-m-d H:i:s');
            echo "08 - Atualização do atributo de ordens recusadas foi realizada com sucesso";
            Log::debug('08 - Iniciado em '.$hi.' e conluído em '.$hf. '  - Atualização do atributo de ordens recusadas foi realizada com sucesso');

        } catch (\Throwable $th) {
            Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
            Log::error("Erro ao cadastrar novos credenciados: " . $th->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);
        }      
    }
    public function callSPCreateQuotationsMap(){
        try {           
            $hi = new \DateTime();
            $hi = $hi->format('Y-m-d H:i:s');
            DB::connection('mysql_mundimed_v1')->select('call st_truncate_quotation_maps()');
            $systems = DB::connection('mysql_mundimed_v1')->select('select id from systems where situation_id = 1');
            $systems = array_map('current', $systems);
            foreach ($systems as $key => $i) {
                DB::connection('mysql_mundimed_v1')->select('call sp_create_quotations_map('.$i.')');
            }
            $hf = new \DateTime();
            $hf = $hf->format('Y-m-d H:i:s');
            echo "09 - Cadastro do MAPA de Cotações foi realizada com sucesso";
            Log::debug('09 - Iniciado em '.$hi.' e conluído em '.$hf. '  - Cadastro do MAPA de Cotações foi realizada com sucesso');
       } catch (\Throwable $th) {
            Log::error("09 - Erro ao acessar o banco de dados: " . $th->getMessage());
            Log::error("09 - Erro ao cadastrar novos credenciados: " . $th->getMessage());
            return response()->json(['erro' => '09 - Ocorreu um erro na operação com o banco de dados.'], 500);
        }      
    }

    public function index()
    {
        $hi = new \DateTime();
        $hi = $hi->format('Y-m-d H:i:s');
        try {
            $tables = DB::connection('mysql_mundimed_v1')->select('SHOW TABLES LIKE "xdb%"');
            $tables = array_map('current', $tables);
           // $systems = DB::connection('mysql_mundimed_v1')->select('select id from systems where situation_id = 1');
           // $systems = array_map('current', $systems);
           //        foreach ($systems as $key => $i) {
            $qtd = intval(env('SYSTEMS_QUANTITY'));
            for($i = 1; $i<=$qtd;$i++){
    
                $now = new \DateTime();
                $now = $now->format('Y-m-d H:i:s');    
                foreach ($tables as $key => $tab) {
                    $db = substr($tab,0,8);
                    $tb = substr_replace($tab, "", 0, 8);
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
            Log::debug('02 (0) - Iniciado em '.$hi.' e conluído em '.$hf);
        } catch (\Throwable $th) {
            Log::error("02 - Erro ao identificar todas as tabelas: " . $th->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);
        }
    }

    public function index1()
    {
        $hi = new \DateTime();
        $hi = $hi->format('Y-m-d H:i:s');
        try {
            $tables = DB::connection('mysql_mundimed_v1')->select('SHOW TABLES LIKE "xdb%"');
            $tables = array_map('current', $tables);
            $systems = DB::connection('mysql_mundimed_v1')->select('select id from systems where situation_id = 1 and sorting = 1');
            $systems = array_map('current', $systems);
            foreach ($systems as $key => $i) {
                $now = new \DateTime();
                $now = $now->format('Y-m-d H:i:s');    
                foreach ($tables as $key => $tab) {
                    $db = substr($tab,0,8);
                    $tb = substr_replace($tab, "", 0, 8);
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
            Log::debug('02 (1) - Iniciado em '.$hi.' e conluído em '.$hf);
        } catch (\Throwable $th) {
            Log::error("02 - Erro ao identificar todas as tabelas: " . $th->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);
        }
    }
    public function index2()
    {
        $hi = new \DateTime();
        $hi = $hi->format('Y-m-d H:i:s');
        try {
            $tables = DB::connection('mysql_mundimed_v1')->select('SHOW TABLES LIKE "xdb%"');
            $tables = array_map('current', $tables);
            $systems = DB::connection('mysql_mundimed_v1')->select('select id from systems where situation_id = 1 and sorting = 2');
            $systems = array_map('current', $systems);
            foreach ($systems as $key => $i) {
                $now = new \DateTime();
                $now = $now->format('Y-m-d H:i:s');    
                foreach ($tables as $key => $tab) {
                    $db = substr($tab,0,8);
                    $tb = substr_replace($tab, "", 0, 8);
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
            Log::debug('02 (2) - Iniciado em '.$hi.' e conluído em '.$hf);
        } catch (\Throwable $th) {
            Log::error("02 - Erro ao identificar todas as tabelas: " . $th->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);
        }
    }
    public function index3()
    {
        $hi = new \DateTime();
        $hi = $hi->format('Y-m-d H:i:s');
        try {
            $tables = DB::connection('mysql_mundimed_v1')->select('SHOW TABLES LIKE "xdb%"');
            $tables = array_map('current', $tables);
            $systems = DB::connection('mysql_mundimed_v1')->select('select id from systems where situation_id = 1 and sorting = 3');
            $systems = array_map('current', $systems);
            foreach ($systems as $key => $i) {
                $now = new \DateTime();
                $now = $now->format('Y-m-d H:i:s');    
                foreach ($tables as $key => $tab) {
                    $db = substr($tab,0,8);
                    $tb = substr_replace($tab, "", 0, 8);
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
            Log::debug('02 (3) - Iniciado em '.$hi.' e conluído em '.$hf);
        } catch (\Throwable $th) {
            Log::error("02 - Erro ao identificar todas as tabelas: " . $th->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);
        }
    }
    public function index4()
    {
        $hi = new \DateTime();
        $hi = $hi->format('Y-m-d H:i:s');
        try {
            $tables = DB::connection('mysql_mundimed_v1')->select('SHOW TABLES LIKE "xdb%"');
            $tables = array_map('current', $tables);
            $systems = DB::connection('mysql_mundimed_v1')->select('select id from systems where situation_id = 1 and sorting = 4');
            $systems = array_map('current', $systems);
            foreach ($systems as $key => $i) {
                $now = new \DateTime();
                $now = $now->format('Y-m-d H:i:s');    
                foreach ($tables as $key => $tab) {
                    $db = substr($tab,0,8);
                    $tb = substr_replace($tab, "", 0, 8);
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
            Log::debug('02 (4) - Iniciado em '.$hi.' e conluído em '.$hf);
        } catch (\Throwable $th) {
            Log::error("02 - Erro ao identificar todas as tabelas: " . $th->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);
        }
    }
    public function index5()
    {
        $hi = new \DateTime();
        $hi = $hi->format('Y-m-d H:i:s');
        try {
            $tables = DB::connection('mysql_mundimed_v1')->select('SHOW TABLES LIKE "xdb%"');
            $tables = array_map('current', $tables);
            $systems = DB::connection('mysql_mundimed_v1')->select('select id from systems where situation_id = 1 and sorting = 5');
            $systems = array_map('current', $systems);
            foreach ($systems as $key => $i) {
                $now = new \DateTime();
                $now = $now->format('Y-m-d H:i:s');    
                foreach ($tables as $key => $tab) {
                    $db = substr($tab,0,8);
                    $tb = substr_replace($tab, "", 0, 8);
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
            Log::debug('02 (5) - Iniciado em '.$hi.' e conluído em '.$hf);
        } catch (\Throwable $th) {
            Log::error("02 - Erro ao identificar todas as tabelas: " . $th->getMessage());
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
            
            $systemId = null;
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
            case 'medicamentos_funeas': $field = 'id';break; 
            case 'mov_avalicao_os': $field = 'id';break; 
            case 'mov_orcamento_os': $field = 'orc_codigo';break; 
            case 'mov_orcamento_os_itens': $field = 'mov_orcamento_os_orc_codigo';break; 
            case 'mov_orcamento_os_itens_lote': $field = 'oit_sequencia';break; 
            case 'mov_os': $field = 'os_codigo';break; 
            case 'mov_os_check_list': $field = 'mov_orcamento_os_orc_codigo';break; 
            case 'mov_os_endereco': $field = 'id_endereco';break; 
            case 'mov_os_lote': $field = 'id_lote';break; 
            case 'mov_os_obs': $field = 'pob_sequencia';break; 
            case 'mov_os_orcamentistas': $field = 'os_data_cad';break; 
            case 'mov_os_status': $field = 'id';break;// ?
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

        if($systemId != 11 && $tb == 'medicamentos_funeas'){
            $isValid = false;
        }
        
        if($isValid){

            try {
                $value = DB::connection('mysql_mundimed_v1')->table($db.$tb)->select($field)->where('system_id', $systemId)->orderBy($field, 'desc')->limit(1)->first();
            } catch (\Throwable $th) {
                Log::error("Erro ao acessar o banco de dados: " . $th->getMessage());
                Log::error("Erro na " . $db.$tb);
                return response()->json(['erro' => 'Ocorreu um erro na operação com o banco de dados.'], 500);      
            }

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
                $systemId = null;
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
