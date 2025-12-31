<?php

App::uses('AppModel', 'Model');
App::import('Component', 'Funcionalidades');

/**
 * GlbQuestionarioResposta Model
 *
 */
class Relatorio extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = false;

    public function pega_conexoes() {

        $SQL = "SELECT * from sysapp_config_empresas;";
        $infoBancos = $this->query($SQL);

        foreach ($infoBancos as $key) {
            foreach ($key as $value) {

                $nome_empresa = $value['nome_empresa'];
                $host = $value['hostname_banco'];
                $db = $value['nome_banco'];
                $user = $value['usuario_banco'];
                $password = $this->DeCrypt($value['senha_banco']);
                $porta = $value['porta_banco'];
                $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

                try {

                    $dbconn = pg_connect($conn_string);
                } catch (Exception $e) {
                    echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
                    return false;
                }

                if ($dbconn != FALSE) {
                    if (!pg_connection_busy($dbconn)) {
                        $SQL = "select '" . $value['nome_empresa'] . "' as nome_empresa, retorna_farol_conexao()";
                        pg_send_query($dbconn, $SQL);
                        $result = pg_get_result($dbconn);
                        $infoConexao[] = pg_fetch_all($result);
                    } else {
                        return array("Nao foi possivel conectar");
                    }
                }
            }
        }
        return $infoConexao;
    }

    Public Function DeCrypt($texto) {
        $G = 0;
        $salasana = 0;
        $Decrypted = '';
        for ($tt = 0; $tt < strlen($texto); $tt++) {
            $sana = ord(substr($texto, $tt, 1));
            $G = $G + 1;
            if ($G == 6) {
                $G = 0;
            }
            $X1 = 0;
            if ($G == 0) {
                $X1 = $sana + ($salasana - 2);
            }
            if ($G == 1) {
                $X1 = $sana - ($salasana - 5);
            }
            if ($G == 2) {
                $X1 = $sana + ($salasana - 4);
            }
            if ($G == 3) {
                $X1 = $sana - ($salasana - 2);
            }
            if ($G == 4) {
                $X1 = $sana + ($salasana - 3);
            }
            if ($G == 5) {
                $X1 = $sana - ($salasana - 5);
            }
            $X1 = $X1 - $G;
            $Decrypted = $Decrypted . chr($X1);
        }
        return $Decrypted;
    }

    Public Function Crypt($texto) {
        $G = 0;
        $salasana = 0;
        $Encrypted = '';
        for ($tt = 0; $tt < strlen($texto); $tt++) {
            $sana = ord(substr($texto, $tt, 1));
            $G = $G + 1;
            if ($G == 6) {
                $G = 0;
            }
            $X1 = 0;
            if ($G == 0) {
                $X1 = $sana - ($salasana - 2);
            }
            if ($G == 1) {
                $X1 = $sana + ($salasana - 5);
            }
            if ($G == 2) {
                $X1 = $sana - ($salasana - 4);
            }
            if ($G == 3) {
                $X1 = $sana + ($salasana - 2);
            }
            if ($G == 4) {
                $X1 = $sana - ($salasana - 3);
            }
            if ($G == 5) {
                $X1 = $sana + ($salasana - 5);
            }
            $X1 = $X1 + $G;
            $Encrypted = $Encrypted . chr($X1);
        }
        return $Encrypted;
    }

    public function relatorio_vendas_vendedor($parametros_relatorio) {

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

        $SQL = "";
        $SQL .= "SELECT   0                     AS cd_tipo                    	, ";
        $SQL .= "''::CHARACTER VARYING AS ds_tipo                    			, ";
        $SQL .= "tbl_vendas_metas.cd_usu                                        , ";
        $SQL .= "MAX(nm_usu) AS nm_usu                               			, ";
        $SQL .= "tbl_vendas_metas.cd_filial                                     , ";
        $SQL .= "SUM(vlr_lanc)::NUMERIC(14,2)     AS vlr_lanc        			, ";
        $SQL .= "SUM(metas)::   NUMERIC(14,2)     AS metas           			, ";
        $SQL .= "SUM(vlr_desconto)                AS vlr_desconto    			, ";
        $SQL .= "SUM(itens)                       AS itens           			, ";
        $SQL .= "SUM(vendas)::INTEGER                      AS vendas          	, ";
        $SQL .= "SUM(ABS(vendas_trocas))          AS vendas_trocas   			, ";
        $SQL .= "SUM(itens_troca)                 AS itens_troca     			, ";
        $SQL .= "SUM(vendas) - SUM(vendas_trocas) AS vendas_sem_troca			, ";
        $SQL .= "(SUM(itens)  - SUM(itens_troca))::INTEGER   AS itens_saldo     , ";
        $SQL .= "CASE ";
        $SQL .= "WHEN SUM(metas):: NUMERIC(14,2) = 0    OR     SUM(vendas)::NUMERIC(14,2) = 0 ";
        $SQL .= "THEN 0 ";
        $SQL .= "ELSE ";
        $SQL .= "(SUM(vlr_lanc) / SUM(metas)::   NUMERIC(14,2)) * 100 ";
        $SQL .= "END as percent_real, ";
        $SQL .= "CASE ";
        $SQL .= "WHEN SUM(itens)::       NUMERIC(14,2) = 0     OR       SUM(vlr_lanc)::NUMERIC(14,2) = 0 ";
        $SQL .= "THEN 0 ";
        $SQL .= "WHEN (SUM(itens) - SUM(itens_troca))::NUMERIC(14,2) = 0 ";
        $SQL .= "THEN 0 ";
        $SQL .= "ELSE SUM(vlr_lanc)::NUMERIC(14,2) / (SUM(itens) - SUM(itens_troca))::NUMERIC(14,2) ";
        $SQL .= "END  AS vlr_medio_prod, ";
        $SQL .= "CASE ";
        $SQL .= "WHEN SUM(vendas)::      NUMERIC(14,2) = 0    OR       SUM(vlr_lanc)::NUMERIC(14,2) = 0 ";
        $SQL .= "THEN 0 ";
        $SQL .= "WHEN (SUM(vendas) - SUM(ABS(vendas_trocas)))::NUMERIC(14,2) = 0 ";
        $SQL .= "THEN 0 ";
        $SQL .= "ELSE SUM(vlr_lanc)::NUMERIC(14,2) / (SUM(vendas) - SUM(ABS(vendas_trocas)))::NUMERIC(14,2) ";
        $SQL .= "END as ticket_medio, ";
        $SQL .= "prc_filial.nm_fant ";
        $SQL .= "FROM     ( SELECT  0                    AS                      cd_tipo, ";
        $SQL .= "''::CHARACTER VARYING AS                      ds_tipo, ";
        $SQL .= "cd_usu_cad            AS                      cd_usu , ";
        $SQL .= "''                    AS                      nm_usu , ";
        $SQL .= "cd_filial                                            , ";
        $SQL .= "0                        AS vlr_lanc                                        , ";
        $SQL .= "0                        AS metas                                           , ";
        $SQL .= "0                        AS vlr_desconto                                    , ";
        $SQL .= "0                        AS itens                                           , ";
        $SQL .= "0                        AS vendas                                          , ";
        $SQL .= "COUNT(*) * -1            AS vendas_trocas                                   , ";
        $SQL .= "SUM(qtde_trocas)::bigint AS itens_troca ";
        $SQL .= "FROM     ( SELECT  orc.cd_filial , ";
        $SQL .= "orc.cd_usu_cad , ";
        $SQL .= "cd_tipo_pgto   , ";
        $SQL .= "ds_tipo_pgto   , ";
        $SQL .= "SUM(qtde_produto_orig) AS qtde_trocas ";
        $SQL .= "FROM     dm_orcamento_vendas_consolidadas_itens_troca troca, ";
        $SQL .= "( SELECT  cd_emp                                  , ";
        $SQL .= "cd_usu_cad                               , ";
        $SQL .= "cd_filial                                , ";
        $SQL .= "cd_pedido                                , ";
        $SQL .= "ano                                      , ";
        $SQL .= "mes                                      , ";
        $SQL .= "tp_pagto ";
        $SQL .= "FROM     dm_orcamento_vendas_consolidadas ";
        $SQL .= "WHERE    cd_filial IN ($parametros_relatorio[0]) ";
        $SQL .= "AND      dt_emi_pedido BETWEEN $parametros_relatorio[2] AND      $parametros_relatorio[1] ";
        $SQL .= "AND      dm_orcamento_vendas_consolidadas.vlr_total_produto::NUMERIC(18,2) <> dm_orcamento_vendas_consolidadas.vlr_devolucao::NUMERIC(18,2) ";
        $SQL .= "GROUP BY cd_emp    , ";
        $SQL .= "cd_usu_cad, ";
        $SQL .= "cd_filial , ";
        $SQL .= "cd_pedido , ";
        $SQL .= "ano       , ";
        $SQL .= "mes       , ";
        $SQL .= "tp_pagto ) ";
        $SQL .= "orc ";
        $SQL .= "INNER JOIN glb_tp_pgto ";
        $SQL .= "ON       orc.cd_emp   = glb_tp_pgto.cd_emp ";
        $SQL .= "AND      orc.tp_pagto = glb_tp_pgto.cd_tipo_pgto ";
        $SQL .= "WHERE    orc.cd_filial         = troca.cd_filial ";
        $SQL .= "AND      orc.cd_pedido         = troca.cd_pedido ";
        $SQL .= "AND      orc.ano               = troca.ano ";
        $SQL .= "AND      orc.mes               = troca.mes ";
        $SQL .= "AND      troca.cd_filial IN ($parametros_relatorio[0]) ";
        $SQL .= "AND      troca.dt_emi_pedido BETWEEN $parametros_relatorio[2] AND      $parametros_relatorio[1] ";
        $SQL .= "GROUP BY orc.cd_filial , ";
        $SQL .= "cd_tipo_pgto  , ";
        $SQL .= "ds_tipo_pgto  , ";
        $SQL .= "orc.cd_usu_cad, ";
        $SQL .= "orc.cd_pedido ) ";
        $SQL .= "tmp ";
        $SQL .= "GROUP BY cd_usu_cad, ";
        $SQL .= "cd_tipo   , ";
        $SQL .= "ds_tipo   , ";
        $SQL .= "cd_filial ";
        $SQL .= "UNION ALL ";
        $SQL .= "SELECT   0                     AS cd_tipo, ";
        $SQL .= "''::CHARACTER VARYING AS ds_tipo, ";
        $SQL .= "cd_usu_cad            AS cd_usu , ";
        $SQL .= "''                    AS nm_usu , ";
        $SQL .= "cd_filial                       , ";
        $SQL .= "0                        AS vlr_lanc                   , ";
        $SQL .= "0                        AS metas                      , ";
        $SQL .= "0                        AS vlr_desconto               , ";
        $SQL .= "0                        AS itens                      , ";
        $SQL .= "0                        AS vendas                     , ";
        $SQL .= "COUNT(*)                 AS vendas_trocas              , ";
        $SQL .= "SUM(qtde_trocas)::bigint AS itens_troca ";
        $SQL .= "FROM     ( SELECT  orc.cd_filial , ";
        $SQL .= "orc.cd_usu_cad , ";
        $SQL .= "cd_tipo_pgto   , ";
        $SQL .= "ds_tipo_pgto   , ";
        $SQL .= "SUM(qtde_produto_orig) AS qtde_trocas ";
        $SQL .= "FROM     dm_orcamento_vendas_consolidadas_itens_troca troca, ";
        $SQL .= "( SELECT  cd_emp                                  , ";
        $SQL .= "cd_usu_cad                               , ";
        $SQL .= "cd_filial                                , ";
        $SQL .= "cd_pedido                                , ";
        $SQL .= "ano                                      , ";
        $SQL .= "mes                                      , ";
        $SQL .= "tp_pagto ";
        $SQL .= "FROM     dm_orcamento_vendas_consolidadas ";
        $SQL .= "WHERE    cd_filial IN ($parametros_relatorio[0]) ";
        $SQL .= "AND      dt_emi_pedido BETWEEN $parametros_relatorio[2] AND      $parametros_relatorio[1] ";
        $SQL .= "AND      dm_orcamento_vendas_consolidadas.vlr_total_produto::NUMERIC(18,2) = dm_orcamento_vendas_consolidadas.vlr_devolucao::NUMERIC(18,2) ";
        $SQL .= "GROUP BY cd_emp    , ";
        $SQL .= "cd_usu_cad, ";
        $SQL .= "cd_filial , ";
        $SQL .= "cd_pedido , ";
        $SQL .= "ano       , ";
        $SQL .= "mes       , ";
        $SQL .= "tp_pagto ) ";
        $SQL .= "orc ";
        $SQL .= "INNER JOIN glb_tp_pgto ";
        $SQL .= "ON       orc.cd_emp   = glb_tp_pgto.cd_emp ";
        $SQL .= "AND      orc.tp_pagto = glb_tp_pgto.cd_tipo_pgto ";
        $SQL .= "WHERE    orc.cd_filial         = troca.cd_filial ";
        $SQL .= "AND      orc.cd_pedido         = troca.cd_pedido ";
        $SQL .= "AND      orc.ano               = troca.ano ";
        $SQL .= "AND      orc.mes               = troca.mes ";
        $SQL .= "AND      troca.cd_filial IN ($parametros_relatorio[0]) ";
        $SQL .= "AND      troca.dt_emi_pedido BETWEEN $parametros_relatorio[2] AND      $parametros_relatorio[1] ";
        $SQL .= "GROUP BY orc.cd_filial , ";
        $SQL .= "cd_tipo_pgto  , ";
        $SQL .= "ds_tipo_pgto  , ";
        $SQL .= "orc.cd_usu_cad, ";
        $SQL .= "orc.cd_pedido ) ";
        $SQL .= "tmp ";
        $SQL .= "GROUP BY cd_usu_cad, ";
        $SQL .= "cd_tipo   , ";
        $SQL .= "ds_tipo   , ";
        $SQL .= "cd_filial ";
        $SQL .= "UNION ALL ";
        $SQL .= "SELECT   cd_tipo_pgto                    AS cd_tipo, ";
        $SQL .= "ds_tipo_pgto::CHARACTER VARYING AS ds_tipo, ";
        $SQL .= "cd_usu                                    , ";
        $SQL .= "''::CHARACTER VARYING AS nm_usu           , ";
        $SQL .= "cd_filial                                 , ";
        $SQL .= "0        AS vlr_lanc                             , ";
        $SQL .= "0        AS metas                                , ";
        $SQL .= "0        AS vlr_desconto                         , ";
        $SQL .= "0        AS itens                                , ";
        $SQL .= "COUNT(*) AS vendas                               , ";
        $SQL .= "0        AS vendas_trocas                        , ";
        $SQL .= "0        AS itens_troca ";
        $SQL .= "FROM     ( SELECT DISTINCT seg.cd_usu , ";
        $SQL .= "cd_emp      , ";
        $SQL .= "tp_pagto    , ";
        $SQL .= "dm.cd_filial, ";
        $SQL .= "dm.cd_ped_cred ";
        $SQL .= "FROM             dm_orcamento_vendas_consolidadas dm ";
        $SQL .= "INNER JOIN segu_usu seg ";
        $SQL .= "ON               seg.cd_usu = cd_usu_cad ";
        $SQL .= "WHERE            dt_emi_pedido BETWEEN $parametros_relatorio[2] AND              $parametros_relatorio[1] ";
        $SQL .= "AND              cd_filial IN ($parametros_relatorio[0])) ";
        $SQL .= "tbl_tmp ";
        $SQL .= "INNER JOIN glb_tp_pgto ";
        $SQL .= "ON       tbl_tmp.cd_emp   = glb_tp_pgto.cd_emp ";
        $SQL .= "AND      tbl_tmp.tp_pagto = glb_tp_pgto.cd_tipo_pgto ";
        $SQL .= "GROUP BY cd_usu      , ";
        $SQL .= "cd_filial   , ";
        $SQL .= "cd_tipo_pgto, ";
        $SQL .= "ds_tipo_pgto ";
        $SQL .= "UNION ALL ";
        $SQL .= "SELECT   cd_tipo_pgto                                                   , ";
        $SQL .= "ds_tipo_pgto                                                            , ";
        $SQL .= "cd_usu                                                                  , ";
        $SQL .= "nm_usu                                                                  , ";
        $SQL .= "ped_vd.cd_filial                                                        , ";
        if ($parametros_relatorio[5][0] == "1") {
            $SQL .= "SUM(vlr_lanc) + SUM(vlr_tac) + SUM(vlr_juros_financiamento) AS vlr_lanc , ";
        } else {
            $SQL .= "SUM(vlr_lanc) AS vlr_lanc , ";
        }
        $SQL .= "metas                                                                   , ";
        $SQL .= "SUM(vlr_desconto) AS vlr_desconto                                       , ";
        $SQL .= "SUM(itens)        AS itens                                              , ";
        $SQL .= "vendas                                                                  , ";
        $SQL .= "vendas_trocas                                                           , ";
        $SQL .= "itens_troca ";
        $SQL .= "FROM     ( SELECT  cd_tipo_pgto                                          , ";
        $SQL .= "ds_tipo_pgto                                           , ";
        $SQL .= "cd_usu                                                 , ";
        $SQL .= "MAX(nm_usu)::CHARACTER VARYING AS nm_usu               , ";
        $SQL .= "cd_filial                                              , ";
        $SQL .= "SUM(vl_tot_it - vl_devol_proporcional) AS vlr_lanc     , ";
        $SQL .= "0::NUMERIC                             AS metas        , ";
        $SQL .= "SUM(vlr_desc_it)                       AS vlr_desconto , ";
        $SQL .= "SUM(qtde_produto)                      AS itens        , ";
        $SQL .= "0                                      AS vendas       , ";
        $SQL .= "0                                      AS vendas_trocas, ";
        $SQL .= "0                                      AS itens_troca  , ";
        $SQL .= "cd_ped_cred                                            , ";
        $SQL .= "cd_emp ";
        $SQL .= "FROM     ( SELECT 0                    AS cd_tipo_pgto , ";
        $SQL .= "''::CHARACTER VARYING AS ds_tipo_pgto , ";
        $SQL .= "segu_usu.cd_usu                       , ";
        $SQL .= "segu_usu.nm_usu AS nm_usu             , ";
        $SQL .= "dm_venda.cd_filial                    , ";
        $SQL .= "vl_tot_it                             , ";
        $SQL .= "vl_devol_proporcional                 , ";
        $SQL .= "vlr_desc_it                           , ";
        $SQL .= "qtde_produto                          , ";
        $SQL .= "cd_ped_cred                           , ";
        $SQL .= "dm_venda.cd_emp ";
        $SQL .= "FROM    dm_orcamento_vendas_consolidadas dm_venda ";
        $SQL .= "INNER JOIN segu_usu ";
        $SQL .= "ON      segu_usu.cd_usu = cd_usu_cad ";
        $SQL .= "INNER JOIN glb_tp_pgto ";
        $SQL .= "ON      dm_venda.cd_emp   = glb_tp_pgto.cd_emp ";
        $SQL .= "AND     dm_venda.tp_pagto = glb_tp_pgto.cd_tipo_pgto ";
        $SQL .= "WHERE   dt_emi_pedido BETWEEN $parametros_relatorio[2] AND     $parametros_relatorio[1] ";
        $SQL .= "AND     dm_venda.cd_filial IN ($parametros_relatorio[0])) ";
        $SQL .= "TMP_vlr ";
        $SQL .= "GROUP BY cd_usu       , ";
        $SQL .= "nm_usu       , ";
        $SQL .= "cd_filial    , ";
        $SQL .= "cd_tipo_pgto , ";
        $SQL .= "ds_tipo_pgto , ";
        $SQL .= "cd_ped_cred  , ";
        $SQL .= "cd_emp ) ";
        $SQL .= "TMP , ";
        $SQL .= "ped_vd ";
        $SQL .= "WHERE    tmp.cd_filial   = ped_vd.cd_filial ";
        $SQL .= "AND      tmp.cd_ped_cred = ped_vd.cd_ped ";
        $SQL .= "AND      tmp.cd_emp      = ped_vd.cd_emp ";
        $SQL .= "GROUP BY cd_tipo_pgto     , ";
        $SQL .= "ds_tipo_pgto     , ";
        $SQL .= "cd_usu           , ";
        $SQL .= "nm_usu           , ";
        $SQL .= "ped_vd.cd_filial , ";
        $SQL .= "metas            , ";
        $SQL .= "vendas           , ";
        $SQL .= "vendas_trocas    , ";
        $SQL .= "itens_troca ";
        $SQL .= "UNION ALL ";
        $SQL .= "SELECT   0  AS cd_tipo_pgto                       , ";
        $SQL .= "'' AS ds_tipo_pgto                       , ";
        $SQL .= "segu_usu.cd_usu                          , ";
        $SQL .= "segu_usu.nm_usu                          , ";
        $SQL .= "vw_meta_vendedor.loja_id AS cd_filial    , ";
        $SQL .= "0::NUMERIC               AS vlr_lanc     , ";
        $SQL .= "SUM(valor_periodo)       AS metas        , ";
        $SQL .= "0                        AS vlr_desconto , ";
        $SQL .= "0                        AS itens        , ";
        $SQL .= "0                        AS vendas       , ";
        $SQL .= "0                        AS vendas_trocas, ";
        $SQL .= "0                        AS itens_troca ";
        $SQL .= "FROM     vw_meta_vendedor, ";
        $SQL .= "glb_pessoa      , ";
        $SQL .= "segu_usu        , ";
        $SQL .= "segu_usu_glb_pessoa ";
        $SQL .= "WHERE    segu_usu_glb_pessoa.cd_pessoa = glb_pessoa.cd_pessoa ";
        $SQL .= "AND      segu_usu_glb_pessoa.cd_usu    = segu_usu.cd_usu ";
        $SQL .= "AND      vw_meta_vendedor.id_vendedor  = glb_pessoa.cd_pessoa ";
        $SQL .= "AND      vw_meta_vendedor.loja_id IN ($parametros_relatorio[0]) ";
        $SQL .= "AND      vw_meta_vendedor.dt_inicial >= $parametros_relatorio[2] ";
        $SQL .= "AND      vw_meta_vendedor.dt_final   <= $parametros_relatorio[1] ";
        $SQL .= "AND      tp_periodo                   =1 ";
        $SQL .= "GROUP BY segu_usu.nm_usu, ";
        $SQL .= "segu_usu.cd_usu, ";
        $SQL .= "loja_id ";
        $SQL .= "UNION ALL ";
        $SQL .= "SELECT   0  AS cd_tipo_pgto                 , ";
        $SQL .= "'' AS ds_tipo_pgto                 , ";
        $SQL .= "segu_usu.cd_usu                    , ";
        $SQL .= "segu_usu.nm_usu                    , ";
        $SQL .= "loja_id            AS cd_filial    , ";
        $SQL .= "0::NUMERIC         AS vlr_lanc     , ";
        $SQL .= "SUM(valor_periodo) AS metas        , ";
        $SQL .= "0                  AS vlr_desconto , ";
        $SQL .= "0                  AS itens        , ";
        $SQL .= "0                  AS vendas       , ";
        $SQL .= "0                  AS vendas_trocas, ";
        $SQL .= "0                  AS itens_troca ";
        $SQL .= "FROM     segu_usu_filial_hist_meta_vend his, ";
        $SQL .= "segu_usu                          , ";
        $SQL .= "segu_usu_glb_pessoa ";
        $SQL .= "WHERE    segu_usu_glb_pessoa.cd_pessoa = his.id_vendedor ";
        $SQL .= "AND      segu_usu_glb_pessoa.cd_usu    = segu_usu.cd_usu ";
        $SQL .= "AND      loja_id IN ($parametros_relatorio[0]) ";
        $SQL .= "AND      dt_cad >= $parametros_relatorio[2] ";
        $SQL .= "AND      dt_cad <= $parametros_relatorio[1] ";
        $SQL .= "GROUP BY segu_usu.nm_usu, ";
        $SQL .= "segu_usu.cd_usu, ";
        $SQL .= "loja_id ";
        $SQL .= "UNION ALL ";
        $SQL .= "SELECT   cd_tipo                                  , ";
        $SQL .= "ds_tipo                                  , ";
        $SQL .= "tmp_ncc.cd_usu                           , ";
        $SQL .= "MAX(nm_usu)::CHARACTER VARYING AS nm_usu , ";
        $SQL .= "cd_filial                                , ";
        $SQL .= "SUM(vl_tot_it)    AS vlr_lanc               , ";
        $SQL .= "0::NUMERIC        AS metas                  , ";
        $SQL .= "0                 AS vlr_desconto           , ";
        $SQL .= "SUM(qtde_produto) AS itens                  , ";
        $SQL .= "0                 AS vendas                 , ";
        $SQL .= "0                 AS vendas_trocas          , ";
        $SQL .= "0                 AS itens_troca ";
        $SQL .= "FROM     ( SELECT  0                    AS       cd_tipo       , ";
        $SQL .= "''::CHARACTER VARYING AS       ds_tipo       , ";
        $SQL .= "segu_usu.cd_usu                              , ";
        $SQL .= "segu_usu.nm_usu                                                       AS nm_usu               , ";
        $SQL .= "orc.cd_filial                                                         AS cd_filial            , ";
        $SQL .= "(vlr_credito * (-1))                                                  AS vl_tot_it            , ";
        $SQL .= "0                                                                     AS vl_devol_proporcional, ";
        $SQL .= "0                                                                     AS vlr_desc_it          , ";
        $SQL .= "SUM(est_produto_pedido_vendas_cpl_ncc_itens.qtde_produto_orig * (-1)) AS qtde_produto ";
        $SQL .= "FROM     est_produto_pedido_vendas_cpl_ncc      , ";
        $SQL .= "est_produto_pedido_vendas_cpl_ncc_itens, ";
        $SQL .= "rc_pgto_ncc                            , ";
        $SQL .= "segu_usu                               , ";
        $SQL .= "est_produto_pedido_vendas_cpl orc ";
        $SQL .= "WHERE    rc_pgto_ncc.cd_emp                           = est_produto_pedido_vendas_cpl_ncc.cd_emp ";
        $SQL .= "AND      rc_pgto_ncc.cd_filial_geracao_ncc            = est_produto_pedido_vendas_cpl_ncc.cd_filial ";
        $SQL .= "AND      rc_pgto_ncc.cd_ctr_ncc                       = est_produto_pedido_vendas_cpl_ncc.cd_ctr_ncc ";
        $SQL .= "AND      est_produto_pedido_vendas_cpl_ncc.cd_emp     = est_produto_pedido_vendas_cpl_ncc_itens.cd_emp ";
        $SQL .= "AND      est_produto_pedido_vendas_cpl_ncc.cd_filial  = est_produto_pedido_vendas_cpl_ncc_itens.cd_filial ";
        $SQL .= "AND      est_produto_pedido_vendas_cpl_ncc.cd_ctr_ncc = est_produto_pedido_vendas_cpl_ncc_itens.cd_ctr_ncc ";
        $SQL .= " AND      rc_pgto_ncc.cd_emp                           = 1 ";
        $SQL .= "AND      orc.cd_filial IN ($parametros_relatorio[0]) ";
        $SQL .= "AND      rc_pgto_ncc.dt_cad BETWEEN $parametros_relatorio[2] AND      $parametros_relatorio[1] ";
        $SQL .= "AND      segu_usu.cd_usu                                        = orc.cd_pessoa_fun ";
        $SQL .= "AND      est_produto_pedido_vendas_cpl_ncc_itens.cd_filial_orig = orc.cd_filial ";
        $SQL .= "AND      est_produto_pedido_vendas_cpl_ncc_itens.cd_pedido_orig = orc.cd_pedido ";
        $SQL .= "AND      est_produto_pedido_vendas_cpl_ncc_itens.mes_orig       = orc.mes ";
        $SQL .= "AND      est_produto_pedido_vendas_cpl_ncc_itens.ano_orig       = orc.ano ";
        $SQL .= "GROUP BY segu_usu.cd_usu , ";
        $SQL .= "segu_usu.nm_usu , ";
        $SQL .= "orc.cd_filial   , ";
        $SQL .= "vlr_credito     , ";
        $SQL .= "rc_pgto_ncc.cd_ncc_sequencia ) ";
        $SQL .= "tmp_ncc ";
        $SQL .= "GROUP BY cd_usu    , ";
        $SQL .= "nm_usu    , ";
        $SQL .= "cd_filial , ";
        $SQL .= "cd_tipo   , ";
        $SQL .= "ds_tipo ) ";
        $SQL .= "tbl_vendas_metas, ";
        $SQL .= "prc_filial ";
        $SQL .= "WHERE    tbl_vendas_metas.cd_filial     =       prc_filial.cd_filial ";
        $SQL .= "GROUP BY tbl_vendas_metas.cd_filial , ";
        $SQL .= "tbl_vendas_metas.cd_usu    , ";
        $SQL .= "prc_filial.nm_fant ";
        $SQL .= "ORDER BY cd_filial , ";
        $SQL .= "vlr_lanc DESC";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function relatorio_vendas_por_filial($parametros_relatorio) {

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

        $SQL = " SELECT   0                     AS cd_tipo                    ,
          ''::CHARACTER VARYING AS ds_tipo                    ,
          tbl_vendas_metas.cd_filial                          ,
          SUM(vlr_lanc)::NUMERIC(14,2)                   AS vlr_lanc        ,
          SUM(metas)::   NUMERIC(14,2)                   AS metas           ,
          SUM(vlr_desconto)                              AS vlr_desconto    ,
          SUM(itens)                                     AS itens           ,
          SUM(vendas)::NUMERIC(14,2)                     AS vendas          ,
          SUM(ABS(vendas_trocas))                        AS vendas_trocas   ,
          SUM(itens_troca)                               AS itens_troca     ,
          SUM(vendas) - SUM(vendas_trocas)               AS vendas_sem_troca,
          (SUM(itens) - SUM(itens_troca))::NUMERIC(14,2) AS itens_saldo     ,
         CASE
                  WHEN SUM(metas):: NUMERIC(14,2) = 0    OR     SUM(vendas)::NUMERIC(14,2) = 0
                  THEN 0
                  ELSE (SUM(vlr_lanc) / SUM(metas):: NUMERIC(14,2)) * 100
         END AS percent_real,
         
         CASE
                  WHEN SUM(itens)::       NUMERIC(14,2) = 0     OR       SUM(vlr_lanc)::NUMERIC(14,2) = 0
                  THEN 0
                  WHEN (SUM(itens) - SUM(itens_troca))::NUMERIC(14,2) = 0
                  THEN 0
                  ELSE SUM(vlr_lanc)::NUMERIC(14,2) / (SUM(itens) - SUM(itens_troca))::NUMERIC(14,2)
         END AS vlr_medio_prod,
         
         CASE
                  WHEN SUM(vendas)::      NUMERIC(14,2) = 0    OR       SUM(vlr_lanc)::NUMERIC(14,2) = 0
                  THEN 0
                  WHEN (SUM(vendas) - SUM(ABS(vendas_trocas)))::NUMERIC(14,2) = 0 
                  THEN 0
                  ELSE SUM(vlr_lanc)::NUMERIC(14,2) / (SUM(vendas) - SUM(ABS(vendas_trocas)))::NUMERIC(14,2)
         END AS ticket_medio,
          prc_filial.nm_fant
 FROM     ( SELECT  0                    AS cd_tipo,
                   ''::CHARACTER VARYING AS ds_tipo,
                   cd_usu_cad            AS cd_usu ,
                   ''                    AS nm_usu ,
                   cd_filial                       ,
                   0                        AS vlr_lanc                   ,
                   0                        AS metas                      ,
                   0                        AS vlr_desconto               ,
                   0                        AS itens                      ,
                   0                        AS vendas                     ,
                   COUNT(*) * -1            AS vendas_trocas              ,
                   SUM(qtde_trocas)::bigint AS itens_troca
          FROM     ( SELECT  orc.cd_filial ,
                            orc.cd_usu_cad ,
                            cd_tipo_pgto   ,
                            ds_tipo_pgto   ,
                            SUM(qtde_produto_orig) AS qtde_trocas
                   FROM     dm_orcamento_vendas_consolidadas_itens_troca troca,
                            ( SELECT  cd_emp                                  ,
                                     cd_usu_cad                               ,
                                     cd_filial                                ,
                                     cd_pedido                                ,
                                     ano                                      ,
                                     mes                                      ,
                                     tp_pagto
                            FROM     dm_orcamento_vendas_consolidadas
                            WHERE    cd_filial IN ($parametros_relatorio[0])
                            AND      dt_emi_pedido BETWEEN $parametros_relatorio[2]  AND      $parametros_relatorio[1] 
                            AND      dm_orcamento_vendas_consolidadas.vlr_total_produto::NUMERIC(18,2) <> dm_orcamento_vendas_consolidadas.vlr_devolucao::NUMERIC(18,2)
                            GROUP BY cd_emp    ,
                                     cd_usu_cad,
                                     cd_filial ,
                                     cd_pedido ,
                                     ano       ,
                                     mes       ,
                                     tp_pagto
                            )
                            orc
                            INNER JOIN glb_tp_pgto
                            ON       orc.cd_emp   = glb_tp_pgto.cd_emp
                            AND      orc.tp_pagto = glb_tp_pgto.cd_tipo_pgto
                   WHERE    orc.cd_filial         = troca.cd_filial
                   AND      orc.cd_pedido         = troca.cd_pedido
                   AND      orc.ano               = troca.ano
                   AND      orc.mes               = troca.mes
                   AND      troca.cd_filial IN ($parametros_relatorio[0])
                   AND      troca.dt_emi_pedido BETWEEN $parametros_relatorio[2]  AND      $parametros_relatorio[1] 
                   GROUP BY orc.cd_filial ,
                            cd_tipo_pgto  ,
                            ds_tipo_pgto  ,
                            orc.cd_usu_cad,
                            orc.cd_pedido
                   )
                   tmp
          GROUP BY cd_usu_cad,
                   cd_tipo   ,
                   ds_tipo   ,
                   cd_filial
          
          UNION ALL
          
          SELECT   0                     AS cd_tipo,
                   ''::CHARACTER VARYING AS ds_tipo,
                   cd_usu_cad            AS cd_usu ,
                   ''                    AS nm_usu ,
                   cd_filial                       ,
                   0                        AS vlr_lanc                   ,
                   0                        AS metas                      ,
                   0                        AS vlr_desconto               ,
                   0                        AS itens                      ,
                   0                        AS vendas                     ,
                   COUNT(*)                 AS vendas_trocas              ,
                   SUM(qtde_trocas)::bigint AS itens_troca
          FROM     ( SELECT  orc.cd_filial ,
                            orc.cd_usu_cad ,
                            cd_tipo_pgto   ,
                            ds_tipo_pgto   ,
                            SUM(qtde_produto_orig) AS qtde_trocas
                   FROM     dm_orcamento_vendas_consolidadas_itens_troca troca,
                            ( SELECT  cd_emp                                  ,
                                     cd_usu_cad                               ,
                                     cd_filial                                ,
                                     cd_pedido                                ,
                                     ano                                      ,
                                     mes                                      ,
                                     tp_pagto
                            FROM     dm_orcamento_vendas_consolidadas
                            WHERE    cd_filial IN ($parametros_relatorio[0])
                            AND      dt_emi_pedido BETWEEN $parametros_relatorio[2]  AND      $parametros_relatorio[1] 
                            AND      dm_orcamento_vendas_consolidadas.vlr_total_produto::NUMERIC(18,2) = dm_orcamento_vendas_consolidadas.vlr_devolucao::NUMERIC(18,2)
                            GROUP BY cd_emp    ,
                                     cd_usu_cad,
                                     cd_filial ,
                                     cd_pedido ,
                                     ano       ,
                                     mes       ,
                                     tp_pagto
                            )
                            orc
                            INNER JOIN glb_tp_pgto
                            ON       orc.cd_emp   = glb_tp_pgto.cd_emp
                            AND      orc.tp_pagto = glb_tp_pgto.cd_tipo_pgto
                   WHERE    orc.cd_filial         = troca.cd_filial
                   AND      orc.cd_pedido         = troca.cd_pedido
                   AND      orc.ano               = troca.ano
                   AND      orc.mes               = troca.mes
                   AND      troca.cd_filial IN ($parametros_relatorio[0])
                   AND      troca.dt_emi_pedido BETWEEN $parametros_relatorio[2]  AND      $parametros_relatorio[1] 
                   GROUP BY orc.cd_filial ,
                            cd_tipo_pgto  ,
                            ds_tipo_pgto  ,
                            orc.cd_usu_cad,
                            orc.cd_pedido
                   )
                   tmp
          GROUP BY cd_usu_cad,
                   cd_tipo   ,
                   ds_tipo   ,
                   cd_filial
          
          UNION ALL
          
          SELECT   cd_tipo_pgto                    AS cd_tipo,
                   ds_tipo_pgto::CHARACTER VARYING AS ds_tipo,
                   cd_usu                                    ,
                   ''::CHARACTER VARYING AS nm_usu           ,
                   cd_filial                                 ,
                   0        AS vlr_lanc                             ,
                   0        AS metas                                ,
                   0        AS vlr_desconto                         ,
                   0        AS itens                                ,
                   COUNT(*) AS vendas                               ,
                   0        AS vendas_trocas                        ,
                   0        AS itens_troca
          FROM     ( SELECT DISTINCT seg.cd_usu ,
                                    cd_emp      ,
                                    tp_pagto    ,
                                    dm.cd_filial,
                                    dm.cd_ped_cred
                   FROM             dm_orcamento_vendas_consolidadas dm
                                    INNER JOIN segu_usu seg
                                    ON               seg.cd_usu = cd_usu_cad
                   WHERE            dt_emi_pedido BETWEEN $parametros_relatorio[2]  AND              $parametros_relatorio[1] 
                   AND              cd_filial IN ($parametros_relatorio[0])
                   )
                   tbl_tmp
                   INNER JOIN glb_tp_pgto
                   ON       tbl_tmp.cd_emp   = glb_tp_pgto.cd_emp
                   AND      tbl_tmp.tp_pagto = glb_tp_pgto.cd_tipo_pgto
          GROUP BY cd_usu      ,
                   cd_filial   ,
                   cd_tipo_pgto,
                   ds_tipo_pgto
          
          UNION ALL
          
          SELECT   cd_tipo_pgto                                                            ,
                   ds_tipo_pgto                                                            ,
                   cd_usu                                                                  ,
                   nm_usu                                                                  ,
                   ped_vd.cd_filial                                                        , ";
        if ($parametros_relatorio[5][0] == "1") {
            $SQL .= "SUM(vlr_lanc) + SUM(vlr_tac) + SUM(vlr_juros_financiamento) AS vlr_lanc , ";
        } else {
            $SQL .= "SUM(vlr_lanc) AS vlr_lanc , ";
        }
        $SQL .= "   metas                                                                   ,
                   SUM(vlr_desconto) AS vlr_desconto                                       ,
                   SUM(itens)        AS itens                                              ,
                   vendas                                                                  ,
                   vendas_trocas                                                           ,
                   itens_troca
          FROM     ( SELECT  cd_tipo_pgto                                          ,
                            ds_tipo_pgto                                           ,
                            cd_usu                                                 ,
                            MAX(nm_usu)::CHARACTER VARYING AS nm_usu               ,
                            cd_filial                                              ,
                            SUM(vl_tot_it - vl_devol_proporcional) AS vlr_lanc     ,
                            0::NUMERIC                             AS metas        ,
                            SUM(vlr_desc_it)                       AS vlr_desconto ,
                            SUM(qtde_produto)                      AS itens        ,
                            0                                      AS vendas       ,
                            0                                      AS vendas_trocas,
                            0                                      AS itens_troca  ,
                            cd_ped_cred                                            ,
                            cd_emp
                   FROM     ( SELECT 0                    AS cd_tipo_pgto ,
                                    ''::CHARACTER VARYING AS ds_tipo_pgto ,
                                    segu_usu.cd_usu                       ,
                                    segu_usu.nm_usu AS nm_usu             ,
                                    dm_venda.cd_filial                    ,
                                    vl_tot_it                             ,
                                    vl_devol_proporcional                 ,
                                    vlr_desc_it                           ,
                                    qtde_produto                          ,
                                    cd_ped_cred                           ,
                                    dm_venda.cd_emp
                            FROM    dm_orcamento_vendas_consolidadas dm_venda
                                    INNER JOIN segu_usu
                                    ON      segu_usu.cd_usu = cd_usu_cad
                                    INNER JOIN glb_tp_pgto
                                    ON      dm_venda.cd_emp   = glb_tp_pgto.cd_emp
                                    AND     dm_venda.tp_pagto = glb_tp_pgto.cd_tipo_pgto
                            WHERE   dt_emi_pedido BETWEEN $parametros_relatorio[2]  AND     $parametros_relatorio[1] 
                            AND     dm_venda.cd_filial IN ($parametros_relatorio[0])
                            )
                            TMP_vlr
                   GROUP BY cd_usu       ,
                            nm_usu       ,
                            cd_filial    ,
                            cd_tipo_pgto ,
                            ds_tipo_pgto ,
                            cd_ped_cred  ,
                            cd_emp
                   )
                   TMP,
                   ped_vd
          WHERE    tmp.cd_filial   = ped_vd.cd_filial
          AND      tmp.cd_ped_cred = ped_vd.cd_ped
          AND      tmp.cd_emp      = ped_vd.cd_emp
          GROUP BY cd_tipo_pgto     ,
                   ds_tipo_pgto     ,
                   cd_usu           ,
                   nm_usu           ,
                   ped_vd.cd_filial ,
                   metas            ,
                   vendas           ,
                   vendas_trocas    ,
                   itens_troca
          
          UNION ALL
          
          SELECT   0  AS cd_tipo_pgto                       ,
                   '' AS ds_tipo_pgto                       ,
                   segu_usu.cd_usu                          ,
                   segu_usu.nm_usu                          ,
                   vw_meta_vendedor.loja_id AS cd_filial    ,
                   0::NUMERIC               AS vlr_lanc     ,
                   SUM(valor_periodo)       AS metas        ,
                   0                        AS vlr_desconto ,
                   0                        AS itens        ,
                   0                        AS vendas       ,
                   0                        AS vendas_trocas,
                   0                        AS itens_troca
          FROM     vw_meta_vendedor,
                   glb_pessoa      ,
                   segu_usu        ,
                   segu_usu_glb_pessoa,
                   segu_usu_grp,
                   segu_grp_usu
          WHERE    segu_usu_glb_pessoa.cd_pessoa = glb_pessoa.cd_pessoa
          AND      segu_usu_glb_pessoa.cd_usu    = segu_usu.cd_usu
          AND      vw_meta_vendedor.id_vendedor  = glb_pessoa.cd_pessoa
          AND      segu_usu_grp.cd_usu = segu_usu.cd_usu
          AND      segu_usu_grp.cd_grp = segu_grp_usu.cd_grp
          AND      vw_meta_vendedor.loja_id IN ($parametros_relatorio[0])
          AND      vw_meta_vendedor.dt_inicial >= $parametros_relatorio[2] 
          AND      vw_meta_vendedor.dt_final   <= $parametros_relatorio[1] 
          AND      tp_periodo                   =1
          AND      segu_grp_usu.vendedor = 1
          GROUP BY segu_usu.nm_usu,
                   segu_usu.cd_usu,
                   loja_id
          
          UNION ALL
          
          SELECT   0  AS cd_tipo_pgto                 ,
                   '' AS ds_tipo_pgto                 ,
                   segu_usu.cd_usu                    ,
                   segu_usu.nm_usu                    ,
                   loja_id            AS cd_filial    ,
                   0::NUMERIC         AS vlr_lanc     ,
                   SUM(valor_periodo) AS metas        ,
                   0                  AS vlr_desconto ,
                   0                  AS itens        ,
                   0                  AS vendas       ,
                   0                  AS vendas_trocas,
                   0                  AS itens_troca
          FROM     segu_usu_filial_hist_meta_vend his,
                   segu_usu                          ,
                   segu_usu_glb_pessoa
          WHERE    segu_usu_glb_pessoa.cd_pessoa = his.id_vendedor
          AND      segu_usu_glb_pessoa.cd_usu    = segu_usu.cd_usu
          AND      loja_id IN ($parametros_relatorio[0])
          AND      dt_cad >= $parametros_relatorio[2] 
          AND      dt_cad <= $parametros_relatorio[1] 
          GROUP BY segu_usu.nm_usu,
                   segu_usu.cd_usu,
                   loja_id
          
          UNION ALL
          
          SELECT   cd_tipo                                  ,
                   ds_tipo                                  ,
                   tmp_ncc.cd_usu                           ,
                   MAX(nm_usu)::CHARACTER VARYING AS nm_usu ,
                   cd_filial                                ,
                   SUM(vl_tot_it)    AS vlr_lanc               ,
                   0::NUMERIC        AS metas                  ,
                   0                 AS vlr_desconto           ,
                   SUM(qtde_produto) AS itens                  ,
                   0                 AS vendas                 ,
                   0                 AS vendas_trocas          ,
                   0                 AS itens_troca
          FROM     ( SELECT  0                    AS       cd_tipo       ,
                            ''::CHARACTER VARYING AS       ds_tipo       ,
                            segu_usu.cd_usu                              ,
                            segu_usu.nm_usu                                                       AS nm_usu               ,
                            orc.cd_filial                                                         AS cd_filial            ,
                            (vlr_credito * (-1))                                                  AS vl_tot_it            ,
                            0                                                                     AS vl_devol_proporcional,
                            0                                                                     AS vlr_desc_it          ,
                            SUM(est_produto_pedido_vendas_cpl_ncc_itens.qtde_produto_orig * (-1)) AS qtde_produto
                   FROM     est_produto_pedido_vendas_cpl_ncc      ,
                            est_produto_pedido_vendas_cpl_ncc_itens,
                            rc_pgto_ncc                            ,
                            segu_usu                               ,
                            est_produto_pedido_vendas_cpl orc
                   WHERE    rc_pgto_ncc.cd_emp                           = est_produto_pedido_vendas_cpl_ncc.cd_emp
                   AND      rc_pgto_ncc.cd_filial_geracao_ncc            = est_produto_pedido_vendas_cpl_ncc.cd_filial
                   AND      rc_pgto_ncc.cd_ctr_ncc                       = est_produto_pedido_vendas_cpl_ncc.cd_ctr_ncc
                   AND      est_produto_pedido_vendas_cpl_ncc.cd_emp     = est_produto_pedido_vendas_cpl_ncc_itens.cd_emp
                   AND      est_produto_pedido_vendas_cpl_ncc.cd_filial  = est_produto_pedido_vendas_cpl_ncc_itens.cd_filial
                   AND      est_produto_pedido_vendas_cpl_ncc.cd_ctr_ncc = est_produto_pedido_vendas_cpl_ncc_itens.cd_ctr_ncc
                   AND      rc_pgto_ncc.cd_emp                           = 1
                   AND      orc.cd_filial IN ($parametros_relatorio[0])
                   AND      rc_pgto_ncc.dt_cad BETWEEN $parametros_relatorio[2]  AND      $parametros_relatorio[1]
                   AND      segu_usu.cd_usu                                        = orc.cd_pessoa_fun
                   AND      est_produto_pedido_vendas_cpl_ncc_itens.cd_filial_orig = orc.cd_filial
                   AND      est_produto_pedido_vendas_cpl_ncc_itens.cd_pedido_orig = orc.cd_pedido
                   AND      est_produto_pedido_vendas_cpl_ncc_itens.mes_orig       = orc.mes
                   AND      est_produto_pedido_vendas_cpl_ncc_itens.ano_orig       = orc.ano
                   GROUP BY segu_usu.cd_usu ,
                            segu_usu.nm_usu ,
                            orc.cd_filial   ,
                            vlr_credito     ,
                            rc_pgto_ncc.cd_ncc_sequencia
                   )
                   tmp_ncc
          GROUP BY cd_usu    ,
                   nm_usu    ,
                   cd_filial ,
                   cd_tipo   ,
                   ds_tipo
          )
          tbl_vendas_metas,
          prc_filial
 WHERE    tbl_vendas_metas.cd_filial = prc_filial.cd_filial
 GROUP BY tbl_vendas_metas.cd_filial ,
          prc_filial.nm_fant
 ORDER BY vlr_lanc DESC";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        try {

            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsultaFilial = pg_fetch_all($result);

            return $resultadoConsultaFilial;
        }
    }

    //16134: Relatório de pedidos SysApp
    public function relatorio_pedido_compras($parametros) {
        $SQL = "";
        $SQL .= "SELECT   ped.cd_filial                                                                 , ";
        $SQL .= "         prc_filial.nm_fant                                                            , ";
        $SQL .= "         dm_prod.cd_marca                                                              , ";
        $SQL .= "         dm_prod.cd_linha                                                              , ";
        $SQL .= "         dm_prod.ds_marca                                                              , ";
        $SQL .= "         dm_prod.ds_linha                                                              , ";

        if ($parametros['opt_tp_periodo'] == "FATURAMENTO") {
            $SQL .= "         ped.dt_prev_entrega::date                                                     , ";
        } else {
            $SQL .= "         vw_sald.dt_entrada_nf::date                                                   , ";
        }

        $SQL .= "         COUNT(dm_prod.ds_prod_z)                               AS qtde_pro_z          , ";

        if ($parametros['opt_tp_periodo'] == "FATURAMENTO") {
            $SQL .= "         SUM(ped_itens.qtde_prod)::INTEGER                      AS qtde_produto_itens  , ";
            $SQL .= "         SUM((ped_itens.vlr_tot_produto * ped_itens.qtde_prod)) AS vlr_tot_produt_itens, ";
        } else {
            $SQL .= "         SUM(vw_sald.qtde_nf)::integer as qtde_produto_itens,  ";
            $SQL .= "         SUM((vw_sald.vlr_custo_produto_nf * vw_sald.qtde_nf)) as vlr_tot_produt_itens, ";
        }

        $SQL .= "         CASE ";
        $SQL .= "                  WHEN AVG(dm_pend.pendencia) > 0 ";
        $SQL .= "                  THEN SUM(vw_sald.qtde_pedido - vw_sald.qtde_nf) ";
        $SQL .= "                  WHEN AVG(dm_pend.pendencia) = 0 ";
        $SQL .= "                  THEN 0 ";
        $SQL .= "         END AS qtde_sald_itens_restante, ";
        $SQL .= "         CASE ";
        $SQL .= "                  WHEN SUM((ped_itens.vlr_tot_produto * ped_itens.qtde_prod)) = 0 ";
        $SQL .= "                  THEN 0 ";
        $SQL .= "                  ELSE SUM(ped_itens.qtde_prod * ped_itens.vlr_venda_prazo) / SUM((ped_itens.vlr_tot_produto * ped_itens.qtde_prod)) ";
        $SQL .= "         END AS markup ";
        $SQL .= "FROM     dm_produto dm_prod , ";
        $SQL .= "         prc_filial         , ";
        $SQL .= "         (SELECT  cd_filial , ";
        $SQL .= "                  cd_pedido , ";
        $SQL .= "                  pendencia ";
        $SQL .= "         FROM     dm_est_produto_ped_compra_cpl_pendencia ";
        $SQL .= "         GROUP BY cd_filial, ";
        $SQL .= "                  cd_pedido, ";
        $SQL .= "                  pendencia ";
        $SQL .= "         ) ";
        $SQL .= "         dm_pend                       , ";
        $SQL .= "         est_produto_ped_compra_cpl ped, ";
        $SQL .= "         est_produto_ped_compra_cpl_itens ped_itens ";

        if ($parametros['opt_tp_periodo'] == "FATURAMENTO") {
            $SQL .= "         LEFT JOIN ";
        } else {
            $SQL .= "         INNER JOIN ";
        }

        $SQL .= "                  ( SELECT COALESCE(est_produto_cpl_nf_entrada.nota_numero, 0)      AS nota_numero  , ";
        $SQL .= "                          COALESCE(est_produto_cpl_nf_entrada.dt_cad, '1900-01-01') AS dt_entrada_nf, ";
        $SQL .= "                          dm_est_produto_ped_compra_cpl_pendencia_itens_temp.* ";
        $SQL .= "                  FROM    dm_est_produto_ped_compra_cpl_pendencia_itens_temp ";

        if ($parametros['opt_tp_periodo'] == "FATURAMENTO") {
            $SQL .= "         LEFT JOIN ";
        } else {
            $SQL .= "         INNER JOIN ";
        }

        $SQL .= "                          est_produto_cpl_nf_entrada ";
        $SQL .= "                          ON      est_produto_cpl_nf_entrada.cd_entrada = dm_est_produto_ped_compra_cpl_pendencia_itens_temp.cd_entrada ";
        $SQL .= "                  ) ";
        $SQL .= "                  vw_sald ";
        $SQL .= "         ON       vw_sald.cd_filial      = ped_itens.cd_filial ";
        $SQL .= "         AND      vw_sald.cd_pedido      = ped_itens.cd_pedido ";
        $SQL .= "         AND      vw_sald.cd_cpl_tamanho = ped_itens.cd_cpl_tamanho, ";
        $SQL .= "                  vw_cond_pgto ";
        $SQL .= "WHERE    vw_cond_pgto.cd_cond_pgto = ped.cd_cond_pgto ";

        if ($parametros['param_filtro_adicional'] != null) {

            if ($parametros['param_filtro_adicional']['tipo_filtro'] == "produto") {
                $SQL .= "         AND dm_prod.ds_prod_z like '%" . $parametros['param_filtro_adicional']['valor_campo_pesquisa'] . "%'";
            } else {
                $SQL .= "         AND dm_prod.ds_linha like '%" . $parametros['param_filtro_adicional']['valor_campo_pesquisa'] . "%'";
            }
        }

        $SQL .= "AND      dm_pend.cd_filial         = ped.cd_filial ";
        $SQL .= "AND      dm_pend.cd_pedido         = ped.cd_pedido ";
        $SQL .= "AND      dm_pend.cd_filial         = ped_itens.cd_filial ";
        $SQL .= "AND      dm_pend.cd_pedido         = ped_itens.cd_pedido ";
        $SQL .= "AND      dm_prod.cd_cpl_tamanho    = ped_itens.cd_cpl_tamanho ";

        if ($parametros['opt_pedidos_pendentes'] == "SIM") {
            $SQL .= "   AND dm_pend.pendencia > 0 ";
        } elseif ($parametros['opt_pedidos_pendentes'] == "NAO") {
            $SQL .= "   AND dm_pend.pendencia = 0 ";
        }

        $SQL .= "AND      ped.cd_filial             = prc_filial.cd_filial ";
        $SQL .= "AND      ped.cd_pedido_tp          = 0 ";
        $SQL .= "AND      ped.dt_cancel             < '1900-01-01' ";

        if ($parametros['opt_tp_periodo'] == "FATURAMENTO") {
            $SQL .= "AND      ped.dt_prev_entrega BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'];
        } else {
            $SQL .= "AND      vw_sald.dt_entrada_nf BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'];
        }

        $SQL .= "AND      ped.cd_filial IN (" . $parametros['cd_filial'] . ") ";
        $SQL .= "GROUP BY ped.cd_filial      , ";
        $SQL .= "         dm_prod.cd_marca   , ";
        $SQL .= "         dm_prod.cd_linha   , ";
        $SQL .= "         dm_prod.ds_marca   , ";
        $SQL .= "         dm_prod.ds_linha   , ";

        if ($parametros['opt_tp_periodo'] == "FATURAMENTO") {
            $SQL .= "         ped.dt_prev_entrega, ";
        } else {
            $SQL .= "         vw_sald.dt_entrada_nf, ";
        }

        $SQL .= "         prc_filial.nm_fant ";
        $SQL .= "ORDER BY ped.cd_filial , ";
        $SQL .= "         dm_prod.ds_linha;";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];

        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo($e->getMessage());
        }

        try {
            if (!pg_connection_busy($dbconn)) {
                pg_send_query($dbconn, $SQL);
                $result = pg_get_result($dbconn);

                $resultadoConsulta = pg_fetch_all($result);

                /* if ($resultadoConsulta === false) {
                  echo($SQL);
                  } */

                return $resultadoConsulta;
            }
        } catch (Exception $e) {
            echo($e->getMessage());
        }
    }
    
    //25031: Sugestão de relatório SysApp - Controle de vendas
    public function relatorio_controle_vendas($parametros) {
        
         $SQL  = "";
         $SQL .= "SELECT   tmp.*                                                  , ";
         $SQL .= "         prc_filial.nm_fant                                     , ";
         $SQL .= "         segu_usu.nm_usu                                        , ";
         $SQL .= "         dm_produto.ds_prod_z                                   , ";
         $SQL .= "         est_produto_cpl_tamanho_prc_filial_estoque.qtde_estoque, ";
         $SQL .= "         COALESCE( ";
         $SQL .= "                   (SELECT vlr_prazo ";
         $SQL .= "                   FROM    est_produto_cpl_tamanho_prc_filial_preco preco ";
         $SQL .= "                   WHERE   preco.cd_cpl_tamanho = tmp.cd_cpl_tamanho ";
         $SQL .= "                   AND     preco.cd_filial      = tmp.cd_filial ";
         $SQL .= "                   AND     sts_preco            = 0 ";
         $SQL .= "                   ) ";
         $SQL .= "                  ,0) AS vlr_prazo ";
         $SQL .= "FROM     ( SELECT  dm_orcamento_vendas_consolidadas.cd_emp                              , ";
         $SQL .= "                  dm_orcamento_vendas_consolidadas.cd_filial                            , ";
         $SQL .= "                  cd_usu_cad                                                            , ";
         $SQL .= "                  dm_orcamento_vendas_consolidadas.cd_cpl_tamanho                       , ";
         $SQL .= "                  SUM(vl_tot_it   - vl_devol_proporcional):: NUMERIC(14,2) AS vlr_vendido , ";
         $SQL .= "                  (SUM(vl_tot_it) / SUM(qtde_produto))::     NUMERIC(14,2) AS vlr_venda   , ";
         $SQL .= "                  SUM(vl_devol_proporcional)::               NUMERIC(14,2) AS vlr_prop    , ";
         $SQL .= "                  SUM(qtde_produto)                                        AS qtde_vendida ";
         $SQL .= "         FROM     dm_orcamento_vendas_consolidadas ";
         $SQL .= "         WHERE    dt_emi_pedido::date BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'];
         $SQL .= "         AND      dm_orcamento_vendas_consolidadas.cd_filial IN (" . $parametros['cd_filial'] . ") ";
         $SQL .= "         AND      cd_emp = 1 ";
         $SQL .= "         GROUP BY dm_orcamento_vendas_consolidadas.cd_usu_cad     , ";
         $SQL .= "                  dm_orcamento_vendas_consolidadas.cd_emp         , ";
         $SQL .= "                  cd_usu_cad                                      , ";
         $SQL .= "                  dm_orcamento_vendas_consolidadas.cd_cpl_tamanho , ";
         $SQL .= "                  dm_orcamento_vendas_consolidadas.cd_filial ";
         $SQL .= "         ) ";
         $SQL .= "         TMP                                       , ";
         $SQL .= "         dm_produto                                , ";
         $SQL .= "         est_produto_cpl_tamanho_prc_filial_estoque, ";
         $SQL .= "         prc_filial                                , ";
         $SQL .= "         segu_usu ";
         $SQL .= "WHERE    TMP.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho ";
         $SQL .= "AND      tmp.cd_cpl_tamanho = est_produto_cpl_tamanho_prc_filial_estoque.cd_cpl_tamanho ";
         $SQL .= "AND      tmp.cd_filial      = est_produto_cpl_tamanho_prc_filial_estoque.cd_filial ";
         $SQL .= "AND      tmp.cd_emp         = prc_filial.cd_emp ";
         $SQL .= "AND      tmp.cd_filial      = prc_filial.cd_filial ";
         $SQL .= "AND      tmp.cd_usu_cad     = segu_usu.cd_usu ";
         
          if ($parametros['param_filtro_adicional'] != null) {

            if ($parametros['param_filtro_adicional']['tipo_filtro'] == "produto") {
                $SQL .= "         AND dm_produto.ds_prod_z like '%" . $parametros['param_filtro_adicional']['valor_campo_pesquisa'] . "%'";
            } else {
                $SQL .= "         AND dm_produto.ds_linha like '%" . $parametros['param_filtro_adicional']['valor_campo_pesquisa'] . "%'";
            }
        }
        
         if ($parametros['param_filtro_vendedor'] != null) {
                $SQL .= "         AND segu_usu.nm_usu like '%" . $parametros['param_filtro_vendedor']['valor_campo_pesquisa'] . "%'";
        }
        
         $SQL .= "ORDER BY cd_filial      , ";
         $SQL .= "         segu_usu.nm_usu, ";
         $SQL .= "         ds_prod_z";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];

        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo($e->getMessage());
        }

        try {
            if (!pg_connection_busy($dbconn)) {
                pg_send_query($dbconn, $SQL);
                $result = pg_get_result($dbconn);

                $resultadoConsulta = pg_fetch_all($result);
                
                 /*if ($resultadoConsulta === false) {
                  echo($SQL);
                  }*/ 

                return $resultadoConsulta;
            }
        } catch (Exception $e) {
            echo($e->getMessage());
        }
    }

    //16870: Relatório de Vendas por Condição de pagamento - Sintético
    public function relatorio_vendas_condicao_pagamento($parametros) {

        $SQL = "";
        $SQL .= "SELECT   cd_filial                      , ";
        $SQL .= "         nm_fant                        , ";
        $SQL .= "         DS_TIPO                        , ";

        if ($parametros['optAgrupar'] == "condicao_pagamento") {

            $SQL .= "         CD_COND_PGTO                   , ";
            $SQL .= "         DS_COND_PGTO                   , ";
            $SQL .= "         ds_prazo                       , ";
        } else {
            $SQL .= "         0 as CD_COND_PGTO                   , ";
            $SQL .= "         '' as DS_COND_PGTO                   , ";
            $SQL .= "         '' as ds_prazo                       , ";
        }

        $SQL .= "         SUM(vlr_vd)                  AS vlr_vd     , "; //vlr_venda
        $SQL .= "         SUM(qtde_vendas)             AS qtde_vendas, "; //qtde_vendas
        $SQL .= "         SUM(QTD_PECAS)               AS QTD_PECAS  , ";
        $SQL .= "         SUM(vlr_ent)                 AS vlr_ent    , "; //vlr_entrada
        $SQL .= "         COALESCE(MAX(prazo_medio),0) AS prazo_medio  "; //prazo_medio 
        $SQL .= "FROM     ( SELECT  PRC_FILIAL.cd_filial                                                                                         , ";
        $SQL .= "                  PRC_FILIAL.NM_FANT                                                                                            , ";
        $SQL .= "                  '**TODO O INTERVALO DE DATAS**' AS DT_HR_PED                                                                  , ";
        $SQL .= "                  9999                            AS CD_VEND                                                                    , ";
        $SQL .= "                  '**GRUPO DE VENDEDORES**'       AS nm_usu                                                                     , ";
        $SQL .= "                  PED_VD.SIT_PED                                                                                                , ";

        if ($parametros['booUsarVlrTac'] == true) {
            $SQL .= "                  SUM(RC_LANC.VLR_LANC) AS VLR_VD, ";
        } else {
            $SQL .= "                  (SUM(RC_LANC.VLR_LANC) - SUM(rc_lanc.vlr_tac_pgto) - SUM(rc_lanc.vlr_juros_financiamento_pgto)) AS VLR_VD     , ";
        }

        $SQL .= "                  SUM(RC_LANC.VLR_ENTRADA)                                                                        AS VLR_ENT    , ";
        $SQL .= "                  SUM(PED_VD.QTD_PECAS)                                                                           AS QTD_PECAS  , ";
        $SQL .= "                  COUNT(*)                                                                                        AS QTDE_VENDAS, ";
        $SQL .= "                  GLB_TP_PGTO.DS_TIPO_PGTO                                                                        AS DS_TIPO    , ";
        $SQL .= "                  ''                                                                                              AS OBS_ALT    , ";
        $SQL .= "                  GLB_COND_PGTO.CD_COND_PGTO                                                                                    , ";
        $SQL .= "                  GLB_COND_PGTO.DS_COND_PGTO                                                                                    , ";
        $SQL .= "                  GLB_COND_PGTO.DS_PRAZO                                                                                        , ";
        $SQL .= "                  GLB_COND_PGTO.QT_PARC                                                                                         , ";
        $SQL .= "                  (SELECT AVG(dias_vencto)::NUMERIC(14,2) AS prazo_medio ";
        $SQL .= "                  FROM    glb_cond_pgto_cpl ";
        $SQL .= "                  WHERE   glb_cond_pgto_cpl.cd_cond_pgto = glb_cond_pgto.cd_cond_pgto ";
        $SQL .= "                  AND     glb_cond_pgto_cpl.cd_emp       = 1 ";
        $SQL .= "                  ) AS prazo_medio ";
        $SQL .= "         FROM     PRC_FILIAL   , ";
        $SQL .= "                  PED_VD       , ";
        $SQL .= "                  GLB_TP_PGTO  , ";
        $SQL .= "                  GLB_COND_PGTO, ";
        $SQL .= "                  RC_LANC      , ";
        $SQL .= "                  segu_usu ";
        $SQL .= "         WHERE    PED_VD.CD_EMP        = PRC_FILIAL.CD_EMP ";
        $SQL .= "         AND      PED_VD.CD_FILIAL     = PRC_FILIAL.CD_FILIAL ";
        $SQL .= "         AND      RC_LANC.CD_EMP       = GLB_COND_PGTO.CD_EMP ";
        $SQL .= "         AND      RC_LANC.CD_COND_PGTO = GLB_COND_PGTO.CD_COND_PGTO ";
        $SQL .= "         AND      RC_LANC.CD_EMP       = GLB_TP_PGTO.CD_EMP ";
        $SQL .= "         AND      RC_LANC.CD_TIPO_PGTO = GLB_TP_PGTO.CD_TIPO_PGTO ";
        $SQL .= "         AND      segu_usu.cd_usu      = ped_vd.cd_vend ";
        $SQL .= "         AND      PED_VD.CD_EMP        = RC_LANC.CD_EMP ";
        $SQL .= "         AND      PED_VD.CD_FILIAL     = RC_LANC.CD_FILIAL ";
        $SQL .= "         AND      PED_VD.CD_PED        = RC_LANC.CD_PED ";
        $SQL .= "         AND      GLB_TP_PGTO.CD_EMP   = PED_VD.CD_EMP ";
        $SQL .= "         AND      GLB_TP_PGTO.CD_TIPO_PGTO IN (" . $parametros['cd_tp_pgto'] . ") ";
        $SQL .= "         AND      PRC_FILIAL.CD_FILIAL     IN (" . $parametros['cd_filial'] . ") ";
        $SQL .= "         AND      PED_VD.DT_HR_PED      >= " . $parametros['param_dt_inicial'] . "";
        $SQL .= "         AND      PED_VD.DT_HR_PED      <= " . $parametros['param_dt_final'] . "";
        $SQL .= "         AND      PED_VD.STS_CLI_COMPRA <> 62 ";
        $SQL .= "         AND      glb_cond_pgto.tp_cond_pgto IN (0,1) "; //a vista e a prazo
        $SQL .= "         AND      PED_VD.SIT_PED             IN (0,1) "; //situacao tmb 0 e 1
        $SQL .= "         AND ";
        $SQL .= "                  ( ";
        $SQL .= "                           RC_LANC.STS_LANC NOT IN (3) ";
        $SQL .= "                  OR       RC_LANC.CD_LANC      IN ";
        $SQL .= "                           ( SELECT CD_LANC ";
        $SQL .= "                           FROM    RC_LANC_ALT ";
        $SQL .= "                           WHERE   RC_LANC_ALT.CD_EMP    = RC_LANC.CD_EMP ";
        $SQL .= "                           AND     RC_LANC_ALT.CD_FILIAL = RC_LANC.CD_FILIAL ";
        $SQL .= "                           AND     RC_LANC_ALT.CD_LANC   = RC_LANC.CD_LANC ";
        $SQL .= "                           AND     RC_LANC_ALT.TP_ALT IN (0) ";
        $SQL .= "                           ) ";
        $SQL .= "                  ) ";
        $SQL .= "         AND ";
        $SQL .= "                  ( ";
        $SQL .= "                           RC_LANC.CD_LANC NOT IN ";
        $SQL .= "                           ( SELECT CD_LANC_REF ";
        $SQL .= "                           FROM    RC_LANC_ALT ";
        $SQL .= "                           WHERE   RC_LANC_ALT.CD_EMP      = RC_LANC.CD_EMP ";
        $SQL .= "                           AND     RC_LANC_ALT.CD_FILIAL   = RC_LANC.CD_FILIAL ";
        $SQL .= "                           AND     RC_LANC_ALT.CD_LANC_REF = RC_LANC.CD_LANC ";
        $SQL .= "                           ) ";
        $SQL .= "                  ) ";
        $SQL .= "         GROUP BY PRC_FILIAL.cd_filial      , ";
        $SQL .= "                  PRC_FILIAL.NM_FANT        , ";
        $SQL .= "                  GLB_COND_PGTO.CD_COND_PGTO, ";
        $SQL .= "                  GLB_COND_PGTO.DS_COND_PGTO, ";
        $SQL .= "                  GLB_COND_PGTO.DS_PRAZO    , ";
        $SQL .= "                  GLB_COND_PGTO.QT_PARC     , ";
        $SQL .= "                  GLB_TP_PGTO.DS_TIPO_PGTO  , ";
        $SQL .= "                  PED_VD.SIT_PED ";
        $SQL .= "          ";
        $SQL .= "         UNION ";
        $SQL .= "          ";
        $SQL .= "         SELECT   PRC_FILIAL.cd_filial                                                                                                                                           , ";
        $SQL .= "                  PRC_FILIAL.NM_FANT                                                                                                                                             , ";
        $SQL .= "                  '**TODO O INTERVALO DE DATAS**' AS DT_HR_PED                                                                                                                   , ";
        $SQL .= "                  9999                            AS CD_VEND                                                                                                                     , ";
        $SQL .= "                  '**GRUPO DE VENDEDORES**'       AS nm_usu                                                                                                                      , ";
        $SQL .= "                  PED_VD.SIT_PED                                                                                                                                                 , ";

        if ($parametros['booUsarVlrTac'] == true) {
            $SQL .= "                  SUM(RC_LANC.vlr_lanc * RC_LANC_ALT.MULTIPLICADOR) AS VLR_VD, ";
        } else {
            $SQL .= "                  (SUM(RC_LANC.vlr_lanc * RC_LANC_ALT.MULTIPLICADOR) - SUM(rc_lanc.vlr_tac_pgto) - SUM(rc_lanc.vlr_juros_financiamento_pgto))::NUMERIC(14,2) AS VLR_VD     , ";
        }

        $SQL .= "                  SUM(RC_LANC_ALT.VLR_ENTRADA * RC_LANC_ALT.MULTIPLICADOR)                                                                                         AS VLR_ENT    , ";
        $SQL .= "                  SUM(PED_VD.QTD_PECAS)       * RC_LANC_ALT.MULTIPLICADOR                                                                                          AS QTD_PECAS  , ";
        $SQL .= "                  COUNT(*)                    * RC_LANC_ALT.MULTIPLICADOR                                                                                          AS QTDE_VENDAS, ";
        $SQL .= "                  GLB_TP_PGTO.DS_TIPO_PGTO                                                                                                                         AS DS_TIPO    , ";
        $SQL .= "                  RC_LANC_ALT.OBS_ALT                                                                                                                              AS OBS_ALT    , ";
        $SQL .= "                  GLB_COND_PGTO.CD_COND_PGTO                                                                                                                                     , ";
        $SQL .= "                  GLB_COND_PGTO.DS_COND_PGTO                                                                                                                                     , ";
        $SQL .= "                  GLB_COND_PGTO.DS_PRAZO                                                                                                                                         , ";
        $SQL .= "                  GLB_COND_PGTO.QT_PARC                                                                                                                                          , ";
        $SQL .= "                  (SELECT AVG(dias_vencto)::NUMERIC(14,2) AS prazo_medio ";
        $SQL .= "                  FROM    glb_cond_pgto_cpl ";
        $SQL .= "                  WHERE   glb_cond_pgto_cpl.cd_cond_pgto = glb_cond_pgto.cd_cond_pgto ";
        $SQL .= "                  AND     glb_cond_pgto_cpl.cd_emp       = 1 ";
        $SQL .= "                  ) AS prazo_medio ";
        $SQL .= "         FROM     PRC_FILIAL   , ";
        $SQL .= "                  PED_VD       , ";
        $SQL .= "                  GLB_TP_PGTO  , ";
        $SQL .= "                  GLB_COND_PGTO, ";
        $SQL .= "                  RC_LANC      , ";
        $SQL .= "                  RC_LANC_ALT  , ";
        $SQL .= "                  segu_usu ";
        $SQL .= "         WHERE    PED_VD.CD_EMP         = PRC_FILIAL.CD_EMP ";
        $SQL .= "         AND      PED_VD.CD_FILIAL      = PRC_FILIAL.CD_FILIAL ";
        $SQL .= "         AND      RC_LANC.CD_EMP        = GLB_COND_PGTO.CD_EMP ";
        $SQL .= "         AND      RC_LANC.CD_COND_PGTO  = GLB_COND_PGTO.CD_COND_PGTO ";
        $SQL .= "         AND      RC_LANC.CD_EMP        = GLB_TP_PGTO.CD_EMP ";
        $SQL .= "         AND      RC_LANC.CD_TIPO_PGTO  = GLB_TP_PGTO.CD_TIPO_PGTO ";
        $SQL .= "         AND      segu_usu.cd_usu       = ped_vd.cd_vend ";
        $SQL .= "         AND      PED_VD.CD_EMP         = RC_LANC.CD_EMP ";
        $SQL .= "         AND      PED_VD.CD_FILIAL      = RC_LANC.CD_FILIAL ";
        $SQL .= "         AND      PED_VD.CD_PED         = RC_LANC.CD_PED ";
        $SQL .= "         AND      GLB_TP_PGTO.CD_EMP    = PED_VD.CD_EMP ";
        $SQL .= "         AND      RC_LANC_ALT.CD_EMP    = RC_LANC.CD_EMP ";
        $SQL .= "         AND      RC_LANC_ALT.CD_FILIAL = RC_LANC.CD_FILIAL ";
        $SQL .= "         AND      RC_LANC_ALT.CD_LANC   = RC_LANC.CD_LANC ";
        $SQL .= "         AND      GLB_TP_PGTO.CD_TIPO_PGTO IN (" . $parametros['cd_tp_pgto'] . ") ";
        $SQL .= "         AND      PRC_FILIAL.CD_FILIAL     IN (" . $parametros['cd_filial'] . ") ";
        $SQL .= "         AND      RC_LANC_ALT.DT_ALT >= " . $parametros['param_dt_inicial'] . "";
        $SQL .= "         AND      RC_LANC_ALT.DT_ALT <= " . $parametros['param_dt_final'] . "";
        $SQL .= "         AND      glb_cond_pgto.tp_cond_pgto IN (0,1) ";
        $SQL .= "         AND      PED_VD.SIT_PED             IN (0,1) ";
        $SQL .= "         AND      RC_LANC_ALT.TP_ALT <> 3 ";
        $SQL .= "         AND      RC_LANC_ALT.STS_ALT = 1 ";
        $SQL .= "         GROUP BY PRC_FILIAL.cd_filial      , ";
        $SQL .= "                  PRC_FILIAL.NM_FANT        , ";
        $SQL .= "                  GLB_COND_PGTO.CD_COND_PGTO, ";
        $SQL .= "                  GLB_COND_PGTO.DS_COND_PGTO, ";
        $SQL .= "                  GLB_COND_PGTO.DS_PRAZO    , ";
        $SQL .= "                  RC_LANC_ALT.OBS_ALT       , ";
        $SQL .= "                  GLB_TP_PGTO.DS_TIPO_PGTO  , ";
        $SQL .= "                  PED_VD.SIT_PED            , ";
        $SQL .= "                  GLB_COND_PGTO.QT_PARC     , ";
        $SQL .= "                  rc_lanc_alt.multiplicador ";
        $SQL .= "         ) ";
        $SQL .= "         TMP ";
        $SQL .= "GROUP BY cd_filial    , ";
        $SQL .= "         nm_fant      , ";

        if ($parametros['optAgrupar'] == "condicao_pagamento") {
            $SQL .= "         CD_COND_PGTO , ";
            $SQL .= "         DS_COND_PGTO , ";
            $SQL .= "         ds_prazo, ";
        }

        $SQL .= "         DS_TIPO       ";

        $SQL .= "ORDER BY cd_filial   , ";
        $SQL .= "         DS_TIPO     , ";

        if ($parametros['optAgrupar'] == "condicao_pagamento") {
            $SQL .= "         DS_PRAZO    , ";
            $SQL .= "         DS_COND_PGTO, ";
        }

        $SQL .= "         VLR_VD DESC";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];

        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo($e->getMessage());
        }

        try {
            if (!pg_connection_busy($dbconn)) {
                pg_send_query($dbconn, $SQL);
                $result = pg_get_result($dbconn);

                $resultadoConsulta = pg_fetch_all($result);

                /* if ($resultadoConsulta === false) {
                  echo($SQL);
                  } */

                return $resultadoConsulta;
            }
        } catch (Exception $e) {
            echo($e->getMessage());
        }
    }

    public function analise_lucros($parametros) {

        $SQL = "SELECT nm_fant,
 cd_filial_competencia,
 ds_conta_pai,
 ds_despesa,
 SUM(vlr_despesa) AS vlr_despesa
FROM vw_despesa_lanc_agrupado
WHERE cd_filial_competencia IN (" . $parametros['cd_filial'] . ")
AND cd_despesa_tp IN (" . $parametros['cd_despesa'] . ")
AND cd_hist IN (" . $parametros['cd_categoria'] . ")
AND dt_cad::DATE BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND conta_investimento = 0
GROUP BY nm_fant,
 cd_filial_competencia,
 ds_conta_pai,
 ds_despesa
ORDER BY cd_filial_competencia,
 ds_conta_pai,
 ds_despesa";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    ///11814
    public function prev_financeira_receber($parametros) {


        $SQL = "SELECT ";

        if ($parametros['tp_relatorio'] == "ano") {
            $SQL.=" extract('year' FROM rc_lanc_cpl.dt_vencto) AS ano, ";
        } elseif ($parametros['tp_relatorio'] == "mes") {
            $SQL.=" extract('year' FROM rc_lanc_cpl.dt_vencto) AS ano,
 extract('month' FROM rc_lanc_cpl.dt_vencto) AS mes, ";
        } elseif ($parametros['tp_relatorio'] == "dia") {
            $SQL.=" extract('year' FROM rc_lanc_cpl.dt_vencto) AS ano,
 extract('month' FROM rc_lanc_cpl.dt_vencto) AS mes,
 extract('day' FROM rc_lanc_cpl.dt_vencto) AS dia, ";
        }

        if ($parametros['agrupaFilial'] == true) {
            $SQL.=" vw_pedido_lanc.cd_filial,
 prc_filial.nm_fant, ";
        }

        if ($parametros['agrupaTpPgto'] == true) {

            $SQL.=" GLB_TP_PGTO.cd_tipo_pgto,
 GLB_TP_PGTO.ds_tipo_pgto, ";
        }

        $SQL.="
SUM(sld_parc) AS valor_devedor,
 COUNT(*) AS quantidade,
 SUM(vlr_parc) AS valor_total,
 SUM(vlr_parc) - SUM(sld_parc) AS valor_pago_principal,
 (SUM(sld_parc) / SUM(vlr_parc) * 100)::NUMERIC(14, 3) AS percentual_inadimplencia,
 SUM(
CASE true
WHEN sts_parc = 1
THEN 1
ELSE 0
END) AS quantidade_quitada,
 SUM(
CASE true
WHEN sts_parc <> 1
THEN 1
ELSE 0
END) AS quantidade_em_aberto
FROM vw_pedido_lanc, ";

        if ($parametros['agrupaFilial'] == true) {
            $SQL.=" prc_filial, ";
        }

        $SQL.="
GLB_TP_PGTO,
 rc_lanc_cpl
WHERE vw_pedido_lanc.cd_emp = rc_lanc_cpl.cd_emp
AND vw_pedido_lanc.cd_filial = rc_lanc_cpl.cd_filial
AND vw_pedido_lanc.cd_lanc = rc_lanc_cpl.cd_lanc
AND vw_pedido_lanc.cd_tipo_pgto IN (" . $parametros['cd_tipo_pgto'] . ")
AND sts_lanc <> 3
AND sts_parc NOT IN (3)
AND vw_pedido_lanc.cd_emp = 1
AND vw_pedido_lanc.cd_filial IN (" . $parametros['cd_filial'] . ")
AND dt_vencto BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . " ";

        if ($parametros['agrupaFilial'] == true) {

            $SQL.="
AND vw_pedido_lanc.cd_emp = prc_filial.cd_emp
AND vw_pedido_lanc.cd_filial = prc_filial.cd_filial ";
        }

        $SQL.="
AND vw_pedido_lanc.cd_emp = GLB_TP_PGTO.cd_emp
AND vw_pedido_lanc.cd_tipo_pgto = GLB_TP_PGTO.cd_tipo_pgto";


        $SQL.=" GROUP BY ";

        if ($parametros['tp_relatorio'] == "ano") {
            $SQL.=" extract('year' FROM rc_lanc_cpl.dt_vencto)";
        } elseif ($parametros['tp_relatorio'] == "mes") {
            $SQL.=" extract('year' FROM rc_lanc_cpl.dt_vencto),
 extract('month' FROM rc_lanc_cpl.dt_vencto)";
        } elseif ($parametros['tp_relatorio'] == "dia") {
            $SQL.=" extract('year' FROM rc_lanc_cpl.dt_vencto),
 extract('month' FROM rc_lanc_cpl.dt_vencto),
 extract('day' FROM rc_lanc_cpl.dt_vencto) ";
        }

        if ($parametros['agrupaFilial'] == true) {
            $SQL.=", vw_pedido_lanc.cd_filial,
 prc_filial.nm_fant ";
        }

        if ($parametros['agrupaTpPgto'] == true) {
            $SQL.=", GLB_TP_PGTO.cd_tipo_pgto,
 GLB_TP_PGTO.ds_tipo_pgto ";
        }

        $SQL.= "ORDER BY ";

        if ($parametros['agrupaFilial'] == true) {
            $SQL.=" cd_filial, ";
        }

        if ($parametros['agrupaTpPgto'] == true) {
            $SQL.=" GLB_TP_PGTO.cd_tipo_pgto, ";
        }

        if ($parametros['tp_relatorio'] == "ano") {
            $SQL.=" extract('year' FROM rc_lanc_cpl.dt_vencto) ";
        } elseif ($parametros['tp_relatorio'] == "mes") {
            $SQL.=" extract('year' FROM rc_lanc_cpl.dt_vencto), extract('month' FROM rc_lanc_cpl.dt_vencto) ";
        } elseif ($parametros['tp_relatorio'] == "dia") {
            $SQL.=" extract('month' FROM rc_lanc_cpl.dt_vencto), extract('day' FROM rc_lanc_cpl.dt_vencto) ";
        }

        //print($SQL);
        //exit();


        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";


        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function relatorio_prev_financeira_pagar($parametros) {

       /* $SQL = "SELECT cd_filial,
 nm_fant,
 dup_numero,
 parc,
 numero_titulo,
 dup_dt_emis,
 dup_vlr_desc,
 dup_vlr_desc_provi,
 ds_hist,
 dup_dt_pagto,
 dup_vlr,
 dup_vlr_juros,
 dup_vlr_multa,
 dup_vlr_custas,
 dup_vlr_pagto,
 dup_vlr_saldo,
 (cd_pessoa
|| ' - '
|| nm_pessoa)::text AS cod_nome_forn,
 dup_dt_vencto,
 (SELECT SUM(dup_vlr_saldo)
FROM vw_pag_credito_filial_data vw
WHERE vw2.cd_emp = vw.cd_emp
AND vw2.cd_filial = vw.cd_filial
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw2." . $parametros['tp_periodo'] . " = vw." . $parametros['tp_periodo'] . "
) as dup_vlr_saldo_pdata,
 (SELECT SUM(dup_vlr_desc)
FROM vw_pag_credito_filial_data vw
WHERE vw2.cd_emp = vw.cd_emp
AND vw2.cd_filial = vw.cd_filial
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw2." . $parametros['tp_periodo'] . " = vw." . $parametros['tp_periodo'] . "
) as dup_vlr_desc_pdata,
 (SELECT SUM(dup_vlr_desc_provi)
FROM vw_pag_credito_filial_data vw
WHERE vw2.cd_emp = vw.cd_emp
AND vw2.cd_filial = vw.cd_filial
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw2." . $parametros['tp_periodo'] . " = vw." . $parametros['tp_periodo'] . "
) as dup_vlr_desc_provi_pdata,
 (SELECT SUM(dup_vlr_juros)
FROM vw_pag_credito_filial_data vw
WHERE vw2.cd_emp = vw.cd_emp
AND vw2.cd_filial = vw.cd_filial
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw2." . $parametros['tp_periodo'] . " = vw." . $parametros['tp_periodo'] . "
) as dup_vlr_juros_pdata,
 (SELECT SUM(dup_vlr_multa)
FROM vw_pag_credito_filial_data vw
WHERE vw2.cd_emp = vw.cd_emp
AND vw2.cd_filial = vw.cd_filial
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw2." . $parametros['tp_periodo'] . " = vw." . $parametros['tp_periodo'] . "
) as dup_vlr_multa_pdata,
 (SELECT SUM(dup_vlr_custas)
FROM vw_pag_credito_filial_data vw
WHERE vw2.cd_emp = vw.cd_emp
AND vw2.cd_filial = vw.cd_filial
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw2." . $parametros['tp_periodo'] . " = vw." . $parametros['tp_periodo'] . "
) as dup_vlr_custas_pdata,
 (SELECT SUM(dup_vlr_pagto)
FROM vw_pag_credito_filial_data vw
WHERE vw2.cd_emp = vw.cd_emp
AND vw2.cd_filial = vw.cd_filial
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw2." . $parametros['tp_periodo'] . " = vw." . $parametros['tp_periodo'] . "
) as dup_vlr_pagto_pdata,
 (SELECT SUM(dup_vlr_saldo)
FROM vw_pag_credito_filial_data vw
WHERE vw2.cd_emp = vw.cd_emp
AND vw2.cd_filial = vw.cd_filial
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw2." . $parametros['tp_periodo'] . " = vw." . $parametros['tp_periodo'] . "
) as dup_vlr_saldo_pdata,
 (SELECT SUM(dup_vlr)
FROM vw_pag_credito_filial_data vw
WHERE vw2.cd_emp = vw.cd_emp
AND vw2.cd_filial = vw.cd_filial
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw2." . $parametros['tp_periodo'] . " = vw." . $parametros['tp_periodo'] . "
) as dup_vlr_pdata
FROM vw_pag_credito_filial_data vw2
WHERE cd_emp = 1
AND cd_filial IN (" . $parametros['cd_filial'] . ")
AND cd_hist IN (" . $parametros['cd_categoria'] . ")
AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
ORDER BY cd_filial,
 dup_dt_vencto,
 nm_pessoa,
 ds_hist,
 dup_numero,
 parc";
*/
        
         $SQL = "SELECT cd_filial,
                        nm_fant,
                        dup_numero,
                        parc,
                        numero_titulo,
                        dup_dt_emis,
                        dup_vlr_desc,
                        dup_vlr_desc_provi,
                        ds_hist,
                        dup_dt_pagto,
                        dup_vlr,
                        dup_vlr_juros,
                        dup_vlr_multa,
                        dup_vlr_custas,
                        dup_vlr_pagto,
                        dup_vlr_saldo,
                        (cd_pessoa
                       || ' - '
                       || nm_pessoa)::text AS cod_nome_forn,
                        dup_dt_vencto
                FROM     vw_pag_credito_filial_data vw2
                WHERE   cd_emp = 1
                AND     cd_filial IN (" . $parametros['cd_filial'] . ")
                AND     cd_hist IN (" . $parametros['cd_categoria'] . ")
                AND     " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
                ORDER BY cd_filial , "
                     . $parametros['tp_periodo'] . "";
         
        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            
            //var_dump($SQL);
            
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function prev_financeira_pagar_filial($parametros) {

        /*$SQL = "SELECT
vw_pedido_lanc.cd_filial,
 SUM(sld_parc) AS valor_devedor,
 SUM(COUNT(*)) AS quantidade,
 SUM(vlr_parc) AS valor_total,
 SUM(vlr_parc) - SUM(sld_parc) AS valor_pago_principal,
 (SUM(sld_parc) / SUM(vlr_parc) * 100)::NUMERIC(14, 3) AS percentual_inadimplencia,
 FROM vw_pedido_lanc,
 prc_filial,
 GLB_TP_PGTO,
 rc_lanc_cpl
WHERE vw_pedido_lanc.cd_emp = rc_lanc_cpl.cd_emp
AND vw_pedido_lanc.cd_filial = rc_lanc_cpl.cd_filial
AND vw_pedido_lanc.cd_lanc = rc_lanc_cpl.cd_lanc
AND vw_pedido_lanc.cd_tipo_pgto = 3
AND sts_lanc <> 3
AND sts_parc NOT IN (3)
AND vw_pedido_lanc.cd_emp = 1
AND vw_pedido_lanc.cd_filial IN (" . $parametros['cd_filial'] . ")
AND dt_vencto BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND vw_pedido_lanc.cd_emp = prc_filial.cd_emp
AND vw_pedido_lanc.cd_filial = prc_filial.cd_filial
AND vw_pedido_lanc.cd_emp = GLB_TP_PGTO.cd_emp
AND vw_pedido_lanc.cd_tipo_pgto = GLB_TP_PGTO.cd_tipo_pgto
AND GLB_TP_PGTO.CD_TIPO_PGTO IN (" . $parametros['cd_tipo_pgto'] . ")
GROUP BY vw_pedido_lanc.cd_filial
ORDER BY vw_pedido_lanc.cd_filial";*/

            $SQL = "SELECT cd_filial,
                    SUM(dup_vlr_desc) as dup_vlr_desc,
                    SUM(dup_vlr) as dup_vlr,
                    SUM(dup_vlr_juros) as dup_vlr_juros,
                    SUM(dup_vlr_saldo) as dup_vlr_saldo,
                    SUM(dup_vlr_pagto) as dup_vlr_pagto,
                    SUM(dup_vlr_custas) as dup_vlr_custas,
                    SUM(dup_vlr_multa) as dup_vlr_multa,
                    SUM(dup_vlr_desc_provi) as dup_vlr_desc_provi,
                    SUM(dup_vlr_juros_provi) as dup_vlr_juros_provi
                   FROM vw_pag_credito_filial_data
                   WHERE cd_emp = 1
                   AND cd_filial IN (" . $parametros['cd_filial'] . ")
                   AND cd_hist IN (" . $parametros['cd_categoria'] . ")
                   AND " . $parametros['tp_periodo'] . " BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
                   GROUP BY cd_filial
                   ORDER BY cd_filial ";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            
            //vardump($SQL);
            //exit;
            
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function prev_financeira_pagar_data($parametros) {

        $SQL = "SELECT cd_filial as cd_filial_pdata,
 SUM(dup_vlr_desc) as dup_vlr_desc_pdata,
 SUM(dup_vlr) as dup_vlr_pdata,
 SUM(dup_vlr_juros) as dup_vlr_juros_pdata,
 SUM(dup_vlr_saldo) as dup_vlr_saldo_pdata,
 SUM(dup_vlr_pagto) as dup_vlr_pagto_pdata,
 SUM(dup_vlr_custas) as dup_vlr_custas_pdata,
 SUM(dup_vlr_multa) as dup_vlr_multa_pdata,
 SUM(dup_vlr_desc_provi) as dup_vlr_desc_provi_pdata,
 SUM(dup_vlr_juros_provi) as dup_vlr_juros_provi_pdata,
 dup_dt_vencto as dup_dt_vencto_pdata
FROM vw_pag_credito_filial_data
WHERE cd_emp = 1
AND cd_filial IN (" . $parametros['cd_filial'] . ")
AND cd_hist IN (" . $parametros['cd_categoria'] . ")
AND dup_dt_vencto BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
GROUP BY cd_filial_pdata,
 dup_dt_vencto_pdata
ORDER BY cd_filial_pdata,
 dup_dt_vencto_pdata";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function analise_lucros_filial($parametros) {

        $SQL = "SELECT nm_fant,
 SUM(vlr_despesa) AS vlr_despesa
FROM vw_despesa_lanc_agrupado
WHERE cd_filial_competencia IN (" . $parametros['cd_filial'] . ")
AND cd_despesa_tp IN (" . $parametros['cd_despesa'] . ")
AND cd_hist IN (" . $parametros['cd_categoria'] . ")
AND dt_cad::DATE BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND conta_investimento = 0
GROUP BY nm_fant
ORDER BY nm_fant";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function orcamento_venda($parametros) {

        $SQL = "
SELECT cd_filial,
 cd_pedido,
 nm_pessoa AS nm_cliente,
 nm_usu AS nm_vendedor,
 dt_emi_pedido::DATE AS dt_pedido,
 SUBSTR(hora_pedido::text, 1, 8) AS hora_pedido,
 ( SELECT
CASE
WHEN glb_cond_pgto.cd_cond_pgto = 1
THEN '� VISTA'::text
ELSE lpad(qt_parc::text, 3, '0')::text
END qtde_parcelas
FROM ped_vd_glb_tp_pgto
INNER JOIN glb_cond_pgto
ON ped_vd_glb_tp_pgto.cd_cond_pgto = glb_cond_pgto.cd_cond_pgto
WHERE ped_vd_glb_tp_pgto.cd_ped = ped.cd_ped_cred
AND ped_vd_glb_tp_pgto.cd_filial = ped.cd_filial
AND ped_vd_glb_tp_pgto.cd_emp = ped.cd_emp limit 1
) AS qtde_parcelas,
 vlr_total_produto AS vlr_total_produto
FROM est_produto_pedido_vendas_cpl ped,
 segu_usu usu
WHERE ped.cd_pessoa_fun = usu.cd_usu
AND NOT EXISTS
( SELECT 1
FROM vw_pedido_lanc vw
WHERE ped.cd_emp = vw.cd_emp
AND ped.cd_filial = vw.cd_filial
AND ped.cd_ped_cred = vw.cd_ped
AND sts_lanc <> 3
)
AND dt_emi_pedido::DATE BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND cd_filial IN (" . $parametros['cd_filial'] . ")
AND cd_emp = 1
ORDER BY cd_filial,
 dt_emi_pedido,
 ped.hora_pedido";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function busca_recebimento_parcela($parametros) {

        $SQL = "";

        $SQL = "SELECT rc_pgto.cd_emp,
 rc_pgto.cd_filial_bx,
 prc_filial.nm_fant,
 lpad((extract (hour FROM dt_hr_processamento))::VARCHAR, 2, '0')
|| ' &agrave;s '
|| lpad((extract (hour FROM dt_hr_processamento)+1)::VARCHAR, 2, '0') AS horario_recebimento,
 COUNT(*) AS quantidade,
 SUM(vlr_pgto - troco_rat)::NUMERIC(14, 2) AS valor_recebido,
 (SUM(vlr_pgto - troco_rat) / COUNT(*))::NUMERIC(14, 2) AS valor_medio
FROM rc_pgto,
 rc_lanc,
 prc_filial
WHERE rc_pgto.cd_emp = rc_lanc.cd_emp
AND rc_pgto.cd_filial_bx = prc_filial.cd_filial
AND rc_pgto.cd_emp = prc_filial.cd_emp
AND rc_pgto.cd_filial = rc_lanc.cd_filial
AND rc_pgto.cd_lanc = rc_lanc.cd_lanc
AND rc_pgto.cd_filial_bx IN (" . $parametros['cd_filial'] . ")
AND dt_pag BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND TP_BX IN (0, 2, 4)
AND cd_tipo_pgto = 3
AND sts_lanc <> 3
AND sts_pgto = 0
GROUP BY rc_pgto.cd_emp,
 prc_filial.nm_fant,
 rc_pgto.cd_filial_bx,
 lpad((extract (hour FROM dt_hr_processamento))::VARCHAR, 2, '0')
|| ' &agrave;s '
|| lpad((extract (hour FROM dt_hr_processamento)+1)::VARCHAR, 2, '0')
ORDER BY rc_pgto.cd_emp,
 prc_filial.nm_fant,
 rc_pgto.cd_filial_bx,
 lpad((extract (hour FROM dt_hr_processamento))::VARCHAR, 2, '0')
|| ' &agrave;s '
|| lpad((extract (hour FROM dt_hr_processamento)+1)::VARCHAR, 2, '0')";


        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function fluxo_vendas_hora($parametros) {

        $SQL = "";
        $SQL = "SELECT lpad((extract (hour FROM hora_pedido))::VARCHAR, 2, '0') || ' &agrave;s ' || lpad((extract (hour FROM hora_pedido)+1)::VARCHAR, 2, '0') AS hora,
 COUNT(*) AS quantidade,
 SUM(vlr_total_pedido) AS vlr_total_pedido,
 cd_filial
FROM est_produto_pedido_vendas_cpl
WHERE dt_emi_pedido BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND cd_filial IN (" . $parametros['cd_filial'] . ")
GROUP BY lpad((extract (hour FROM hora_pedido))::VARCHAR, 2, '0') || ' &agrave;s ' || lpad((extract (hour FROM hora_pedido)+1)::VARCHAR, 2, '0'),
 cd_filial
ORDER BY cd_filial, lpad((extract (hour FROM hora_pedido))::VARCHAR, 2, '0') || ' &agrave;s ' || lpad((extract (hour FROM hora_pedido)+1)::VARCHAR, 2, '0')";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function fluxo_orcamento_venda_hora($parametros) {
        $SQL = "";
        $SQL .= "SELECT SUBSTR(hora_pedido::text, 1, 2) AS hora,
 COUNT(*) AS quantidade,
 SUM(vlr_total_pedido) AS vlr_total_pedido,
 cd_filial
FROM est_produto_pedido_vendas_cpl
WHERE dt_emi_pedido BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND cd_filial IN (" . $parametros['cd_filial'] . ")
GROUP BY SUBSTR(hora_pedido::text, 1, 2),
 cd_filial
ORDER BY SUBSTR(hora_pedido::text, 1, 2)";
        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function fluxo_vendas_hora_filial($parametros) {

        $SQL = "";
        $SQL = "SELECT COUNT(*) AS quantidade,
 SUM(vlr_total_pedido) AS vlr_total_pedido,
 cd_filial
FROM est_produto_pedido_vendas_cpl
WHERE dt_emi_pedido BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND cd_filial IN (" . $parametros['cd_filial'] . ")
GROUP BY cd_filial
ORDER BY cd_filial";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function comparativo_vendas($parametros) {

        $SQL = "";
        $SQL = "SELECT cd_filial,
 nm_fant,
 SUM(total_venda_1)::NUMERIC(14, 4) AS total_venda_1,
 SUM(total_venda_2)::NUMERIC(14, 4) AS total_venda_2,
 CASE
WHEN SUM(total_venda_1) = 0
THEN 0
ELSE (((SUM(total_venda_2) / SUM(total_venda_1)) - 1) * 100)::NUMERIC(14, 4)
END AS percent_diferenca
FROM ( SELECT cd_filial,
 nm_fant,
 total_venda AS total_venda_1,
 0 AS total_venda_2
FROM vw_resumo_financeiro_vendas
WHERE cd_emp = 1
AND cd_filial IN (" . $parametros['cd_filial'] . ")
AND dt_hr_ped BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "

UNION ALL

SELECT cd_filial,
 nm_fant,
 0 AS total_venda_1,
 total_venda AS total_venda_2
FROM vw_resumo_financeiro_vendas
WHERE cd_emp = 1
AND cd_filial IN (" . $parametros['cd_filial'] . ")
AND dt_hr_ped BETWEEN " . $parametros['param_dt_inicial_2'] . " AND " . $parametros['param_dt_final_2'] . "
)
tmp
GROUP BY cd_filial,
 nm_fant
ORDER BY cd_filial ";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function entrada_x_vendas($parametros) {

        $SQL = "";
        $SQL .= "SELECT tbl_union.cd_filial,
 tbl_union.cd_marca,
 tbl_union.ds_marca,
 tbl_union.qtde_entrada,
 SUM(qtde_estoque) AS qtde_estoque,
 SUM(vlr_estoque) AS vlr_estoque,
 SUM(qtde_estoque_real) AS qtde_estoque_real,
 SUM(qtde_venda) - SUM(qtde_devolucao) AS qtde_venda,
 SUM(vlr_venda) - SUM(vlr_devolucao) AS vlr_venda,
 CASE
WHEN SUM(qtde_venda) - SUM(qtde_devolucao) = 0
THEN SUM(pcusto):: NUMERIC(14, 2)
ELSE
(SELECT COALESCE(AVG(est_produto_cpl_tamanho_prc_filial_estoque.vlr_custo_gerenc), 0)::NUMERIC(14, 4) AS pcusto
FROM dm_produto
INNER JOIN est_produto_cpl_tamanho_prc_filial_estoque
ON est_produto_cpl_tamanho_prc_filial_estoque.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
INNER JOIN dm_orcamento_vendas_consolidadas dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = est_produto_cpl_tamanho_prc_filial_estoque.cd_cpl_tamanho
WHERE est_produto_cpl_tamanho_prc_filial_estoque.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " AND dm_venda_diaria.dt_vd BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . "
AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")
AND dm_produto.cd_marca = tbl_union.cd_marca
)
END AS pcusto,
 CASE
WHEN
(
SUM(qtde_venda) - SUM(qtde_devolucao)
)
> 0
THEN (SUM(vlr_venda) - SUM(vlr_devolucao))/(SUM(qtde_venda) - SUM(qtde_devolucao))
ELSE 0
END:: NUMERIC(14, 2) AS pvenda,
 SUM(perc_qtde_estoque)::NUMERIC(14, 2) AS perc_qtde_estoque,
 CASE
WHEN
(
SUM(qtde_venda) - SUM(qtde_devolucao)
)
> 0
THEN SUM(qtde_estoque) / (SUM(qtde_venda) - SUM(qtde_devolucao))
ELSE 0
END AS relacao_estoque
FROM ( SELECT dm_venda_diaria.cd_filial,
 dm_produto.cd_marca AS cd_marca,
 dm_produto.ds_marca AS ds_marca,
 0 AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 0 AS vlr_estoque,
 SUM(COALESCE(dm_venda_diaria.qtde_produto, 0)) AS qtde_venda,
 (SUM(COALESCE(dm_venda_diaria.vl_tot_it - dm_venda_diaria.vl_devol_proporcional, 0)))::NUMERIC(14, 2) AS vlr_venda,
 (SUM(COALESCE(dm_venda_diaria.vl_tot_it, 0))):: NUMERIC(14, 2) AS pvenda,
 NULL:: NUMERIC(14, 2) AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0 AS vlr_devolucao,
 0 AS perc_qtde_estoque,
 0 AS perc_vlr_estoque,
 (SELECT COALESCE(SUM(qtde_produto), 0) AS total_entrada
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens,
 dm_produto prod
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.cd_filial = est_produto_cpl_nf_entrada_itens.cd_filial";
        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "";
        }
        $SQL .= " AND est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho = prod.cd_cpl_tamanho
AND dm_produto.cd_marca = prod.cd_marca
AND est_produto_cpl_nf_entrada_itens.cd_filial = dm_venda_diaria.cd_filial
) AS qtde_entrada
FROM dm_produto
LEFT JOIN dm_orcamento_vendas_consolidadas dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda_diaria.dt_vd BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . "
AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY dm_venda_diaria.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca

UNION

SELECT dm_venda_diaria.cd_filial,
 dm_produto.cd_marca AS cd_marca,
 dm_produto.ds_marca AS ds_marca,
 0 AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 0 AS vlr_estoque,
 0 AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 0:: NUMERIC(14, 2) AS pcusto,
 SUM(COALESCE(dm_venda_diaria.qtde_produto_orig, 0)):: NUMERIC(14, 4) AS qtde_devolucao,
 0 AS vlr_devolucao,
 0 AS perc_qtde_estoque,
 0 AS perc_vlr_estoque,
 (SELECT COALESCE(SUM(qtde_produto), 0) AS total_entrada
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens,
 dm_produto prod
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.cd_filial = est_produto_cpl_nf_entrada_itens.cd_filial";
        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "";
        }
        $SQL .= " AND est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho = prod.cd_cpl_tamanho
AND dm_produto.cd_marca = prod.cd_marca
AND est_produto_cpl_nf_entrada_itens.cd_filial = dm_venda_diaria.cd_filial
) AS qtde_entrada
FROM dm_produto
LEFT JOIN dm_orcamento_vendas_consolidadas_itens_troca dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda_diaria.dt_emi_pedido BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . "
AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY dm_venda_diaria.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca

UNION

SELECT est_produto_cpl_tamanho_prc_filial_estoque.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca,
 0:: NUMERIC(14, 4) AS qtde_estoque,
 SUM(est_produto_cpl_tamanho_prc_filial_estoque.qtde_estoque):: NUMERIC(14, 4) AS qtde_estoque_real,
 0:: NUMERIC(14, 2) AS vlr_estoque,
 0:: NUMERIC(14, 4) AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 AVG(est_produto_cpl_tamanho_prc_filial_estoque.vlr_custo_gerenc) AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0:: NUMERIC(14, 4) AS vlr_devolucao,
 0:: NUMERIC(14, 2) AS perc_qtde_estoque,
 0 AS perc_vlr_estoque,
 (SELECT COALESCE(SUM(qtde_produto), 0) AS total_entrada
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens,
 dm_produto prod
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.cd_filial = est_produto_cpl_nf_entrada_itens.cd_filial";
        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "";
        }
        $SQL .= " AND est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho = prod.cd_cpl_tamanho
AND dm_produto.cd_marca = prod.cd_marca
AND est_produto_cpl_nf_entrada_itens.cd_filial = est_produto_cpl_tamanho_prc_filial_estoque.cd_filial
) AS qtde_entrada
FROM dm_produto
INNER JOIN est_produto_cpl_tamanho_prc_filial_estoque
ON est_produto_cpl_tamanho_prc_filial_estoque.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE est_produto_cpl_tamanho_prc_filial_estoque.cd_filial IN (" . $parametros['cd_filial'] . ")";
        if (isset($parametros['param_estoque'])) {
            $SQL .= " " . $parametros['param_estoque'] . "";
        }

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY est_produto_cpl_tamanho_prc_filial_estoque.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca

UNION

SELECT dm_produto_filial.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca,
 0:: NUMERIC(14, 4) AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo)::NUMERIC(14, 2) AS vlr_estoque,
 0:: NUMERIC(14, 4) AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 0 AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0:: NUMERIC(14, 4) AS vlr_devolucao,
 CASE
WHEN tmp.qtde_estoque_total = 0
THEN 0
ELSE ((SUM(dm_produto_filial.qtde_estoque) * 100) / tmp.qtde_estoque_total):: NUMERIC(14, 4)
END AS perc_qtde_estoque,
 CASE
WHEN tmp.qtde_estoque_total = 0
THEN 0
ELSE ((SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo) * 100) / tmp_1.vlr_estoque_total)::NUMERIC(14, 4)
END AS perc_vlr_estoque,
 (SELECT COALESCE(SUM(qtde_produto), 0) AS total_entrada
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens,
 dm_produto prod
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.cd_filial = est_produto_cpl_nf_entrada_itens.cd_filial";
        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "";
        }
        $SQL .= " AND est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho = prod.cd_cpl_tamanho
AND dm_produto.cd_marca = prod.cd_marca
AND est_produto_cpl_nf_entrada_itens.cd_filial = dm_produto_filial.cd_filial
) AS qtde_entrada
FROM ( SELECT SUM(dm_produto_filial.qtde_estoque)::NUMERIC(14, 2) AS qtde_estoque_total
FROM dm_produto
INNER JOIN dm_produto_filial
ON dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " )tmp,
 (SELECT SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo)::NUMERIC(14, 2) AS vlr_estoque_total
FROM dm_produto
INNER JOIN dm_produto_filial
ON dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " )tmp_1,
 dm_produto_filial,
 dm_produto
WHERE dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
AND dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")";
        if (isset($parametros['param_estoque'])) {
            $SQL .= " " . $parametros['param_estoque'] . "";
        }

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY dm_produto.cd_marca,
 dm_produto_filial.cd_filial,
 dm_produto.ds_marca,
 tmp.qtde_estoque_total,
 tmp_1.vlr_estoque_total
)
tbl_union
GROUP BY tbl_union.cd_filial,
 tbl_union.cd_marca,
 tbl_union.ds_marca,
 tbl_union.qtde_entrada
ORDER BY cd_filial,
 qtde_venda DESC";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function RetornaCustoCorreto($parametros, $CodMarca) {
        $SQL = "SELECT COALESCE(AVG(est_produto_cpl_tamanho_prc_filial_estoque.vlr_custo_gerenc), 0)::NUMERIC(14, 4) AS pcusto
FROM dm_produto
INNER JOIN est_produto_cpl_tamanho_prc_filial_estoque
ON est_produto_cpl_tamanho_prc_filial_estoque.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
INNER JOIN dm_orcamento_vendas_consolidadas dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = est_produto_cpl_tamanho_prc_filial_estoque.cd_cpl_tamanho
WHERE est_produto_cpl_tamanho_prc_filial_estoque.cd_filial IN (" . $parametros['cd_filial'] . ")
AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)
AND dm_venda_diaria.dt_vd BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . "
AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if ($CodMarca != "") {
            $SQL .= " AND cd_marca = " . $CodMarca;
        }

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function vendas_estoque_marca($parametros) {

        $SQL = "";
        $SQL .= "SELECT tbl_union.cd_filial,
 tbl_union.cd_marca,
 tbl_union.ds_marca,
 SUM(qtde_estoque) AS qtde_estoque,
 SUM(vlr_estoque) AS vlr_estoque,
 SUM(qtde_estoque_real) AS qtde_estoque_real,
 SUM(qtde_venda) - SUM(qtde_devolucao) AS qtde_venda,
 SUM(vlr_venda) - SUM(vlr_devolucao) AS vlr_venda,
 SUM(pcusto):: NUMERIC(14, 2) AS pcusto,
 CASE
WHEN
(
SUM(qtde_venda) - SUM(qtde_devolucao)
)
> 0
THEN (SUM(vlr_venda) - SUM(vlr_devolucao))/(SUM(qtde_venda) - SUM(qtde_devolucao))
ELSE 0
END:: NUMERIC(14, 2) AS pvenda,
 SUM(perc_qtde_estoque)::NUMERIC(14, 2) AS perc_qtde_estoque,
 CASE
WHEN
(
SUM(qtde_venda) - SUM(qtde_devolucao)
)
> 0
THEN SUM(qtde_estoque) / (SUM(qtde_venda) - SUM(qtde_devolucao))
ELSE 0
END AS relacao_estoque
FROM ( SELECT dm_venda_diaria.cd_filial,
 dm_produto.cd_marca AS cd_marca,
 dm_produto.ds_marca AS ds_marca,
 0 AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 0 AS vlr_estoque,
 SUM(COALESCE(dm_venda_diaria.qtde_produto, 0)) AS qtde_venda,
 (SUM(COALESCE(dm_venda_diaria.vl_tot_it - dm_venda_diaria.vl_devol_proporcional, 0)))::NUMERIC(14, 2) AS vlr_venda,
 (SUM(COALESCE(dm_venda_diaria.vl_tot_it, 0))):: NUMERIC(14, 2) AS pvenda,
 NULL:: NUMERIC(14, 2) AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0 AS vlr_devolucao,
 0 AS perc_qtde_estoque,
 0 AS perc_vlr_estoque
FROM dm_produto
LEFT JOIN dm_orcamento_vendas_consolidadas dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda_diaria.dt_vd BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . "
AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY dm_venda_diaria.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca

UNION

SELECT dm_venda_diaria.cd_filial,
 dm_produto.cd_marca AS cd_marca,
 dm_produto.ds_marca AS ds_marca,
 0 AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 0 AS vlr_estoque,
 0 AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 0:: NUMERIC(14, 2) AS pcusto,
 SUM(COALESCE(dm_venda_diaria.qtde_produto_orig, 0)):: NUMERIC(14, 4) AS qtde_devolucao,
 0 AS vlr_devolucao,
 0 AS perc_qtde_estoque,
 0 AS perc_vlr_estoque
FROM dm_produto
LEFT JOIN dm_orcamento_vendas_consolidadas_itens_troca dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda_diaria.dt_emi_pedido BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . "
AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY dm_venda_diaria.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca

UNION

SELECT est_produto_cpl_tamanho_prc_filial_estoque.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca,
 0:: NUMERIC(14, 4) AS qtde_estoque,
 SUM(est_produto_cpl_tamanho_prc_filial_estoque.qtde_estoque):: NUMERIC(14, 4) AS qtde_estoque_real,
 0:: NUMERIC(14, 2) AS vlr_estoque,
 0:: NUMERIC(14, 4) AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 AVG(est_produto_cpl_tamanho_prc_filial_estoque.vlr_custo_gerenc) AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0:: NUMERIC(14, 4) AS vlr_devolucao,
 0:: NUMERIC(14, 2) AS perc_qtde_estoque,
 0 AS perc_vlr_estoque
FROM dm_produto
INNER JOIN est_produto_cpl_tamanho_prc_filial_estoque
ON est_produto_cpl_tamanho_prc_filial_estoque.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE est_produto_cpl_tamanho_prc_filial_estoque.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " " . $parametros['param_estoque'] . "
GROUP BY est_produto_cpl_tamanho_prc_filial_estoque.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca

UNION

SELECT dm_produto_filial.cd_filial,
 dm_produto.cd_marca,
 dm_produto.ds_marca,
 0:: NUMERIC(14, 4) AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo)::NUMERIC(14, 2) AS vlr_estoque,
 0:: NUMERIC(14, 4) AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 0 AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0:: NUMERIC(14, 4) AS vlr_devolucao,
 CASE
WHEN tmp.qtde_estoque_total = 0
THEN 0
ELSE ((SUM(dm_produto_filial.qtde_estoque) * 100) / tmp.qtde_estoque_total):: NUMERIC(14, 4)
END AS perc_qtde_estoque,
 CASE
WHEN tmp.qtde_estoque_total = 0
THEN 0
ELSE ((SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo) * 100) / tmp_1.vlr_estoque_total)::NUMERIC(14, 4)
END AS perc_vlr_estoque
FROM ( SELECT SUM(dm_produto_filial.qtde_estoque)::NUMERIC(14, 2) AS qtde_estoque_total
FROM dm_produto
INNER JOIN dm_produto_filial
ON dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")
" . $parametros['param_estoque'] . "
)
tmp,
 (SELECT SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo)::NUMERIC(14, 2) AS vlr_estoque_total
FROM dm_produto
INNER JOIN dm_produto_filial
ON dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")
)
tmp_1,
 dm_produto_filial,
 dm_produto
WHERE dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
AND dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " " . $parametros['param_estoque'] . "
GROUP BY dm_produto.cd_marca,
 dm_produto_filial.cd_filial,
 dm_produto.ds_marca,
 tmp.qtde_estoque_total,
 tmp_1.vlr_estoque_total
)
tbl_union
GROUP BY tbl_union.cd_filial,
 tbl_union.cd_marca,
 tbl_union.ds_marca
ORDER BY cd_filial,
 qtde_venda DESC";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function vendas_estoque_grupo($parametros) {

        $SQL = "";
        $SQL .= "SELECT tbl_union.cd_filial,
 tbl_union.ds_familia,
 tbl_union.cd_grupo,
 (tbl_union.ds_familia
|| ' - '
|| tbl_union.ds_grupo)::text AS ds_grupo,
 SUM(qtde_estoque) AS qtde_estoque,
 SUM(vlr_estoque) AS vlr_estoque,
 SUM(qtde_estoque_real) AS qtde_estoque_real,
 SUM(qtde_venda) - SUM(qtde_devolucao) AS qtde_venda,
 SUM(vlr_venda) - SUM(vlr_devolucao) AS vlr_venda,
 AVG(pcusto):: NUMERIC(14, 2) AS pcusto,
 CASE
WHEN SUM(qtde_venda) <> 0
THEN SUM(pvenda)/SUM(qtde_venda)
ELSE 0
END:: NUMERIC(14, 2) AS pvenda,
 SUM(perc_qtde_estoque)::NUMERIC(14, 2) AS perc_qtde_estoque,
 CASE
WHEN
(
SUM(qtde_venda) - SUM(qtde_devolucao)
)
<> 0
THEN SUM(qtde_estoque_real) / (SUM(qtde_venda) - SUM(qtde_devolucao))
ELSE SUM(qtde_estoque_real)
END AS relacao_estoque
FROM ( SELECT TMP.cd_filial,
 TMP.cd_grupo AS cd_grupo,
 TMP.ds_grupo AS ds_grupo,
 TMP.ds_familia,
 SUM(qtde_estoque) AS qtde_estoque,
 SUM(qtde_estoque_real) AS qtde_estoque_real,
 SUM(vlr_estoque) AS vlr_estoque,
 SUM(qtde_venda) AS qtde_venda,
 SUM(vlr_venda) AS vlr_venda,
 SUM(pvenda) AS pvenda,
 SUM(pcusto) AS PCUSTO,
 SUM(qtde_devolucao) AS qtde_devolucao,
 SUM(vlr_devolucao) AS vlr_devolucao,
 SUM(perc_qtde_estoque) AS perc_qtde_estoque,
 SUM(perc_vlr_estoque) AS perc_vlr_estoque
FROM ( SELECT dm_venda_diaria.cd_filial,
 dm_produto.cd_grupo AS cd_grupo,
 dm_produto.ds_grupo AS ds_grupo,
 dm_produto.ds_familia,
 0 AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 0 AS vlr_estoque,
 SUM(COALESCE(dm_venda_diaria.qtde_produto, 0)) AS qtde_venda,
 (SUM(COALESCE(dm_venda_diaria.vl_tot_it - dm_venda_diaria.vl_devol_proporcional, 0)))::NUMERIC(14, 2) AS vlr_venda,
 (SUM(COALESCE(dm_venda_diaria.vl_tot_it, 0))):: NUMERIC(14, 2) AS pvenda,
 NULL:: NUMERIC(14, 2) AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0 AS vlr_devolucao,
 0 AS perc_qtde_estoque,
 0 AS perc_vlr_estoque
FROM dm_produto
LEFT JOIN dm_orcamento_vendas_consolidadas dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda_diaria.dt_vd BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . "
AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY dm_venda_diaria.cd_filial,
 dm_produto.cd_grupo,
 dm_produto.ds_grupo,
 dm_produto.ds_familia

UNION

SELECT dm_venda_diaria.cd_filial,
 dm_produto.cd_grupo AS cd_grupo,
 dm_produto.ds_grupo AS ds_grupo,
 dm_produto.ds_familia,
 0 AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 0 AS vlr_estoque,
 0 AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 0:: NUMERIC(14, 2) AS pcusto,
 SUM(COALESCE(dm_venda_diaria.qtde_produto_orig, 0)):: NUMERIC(14, 4) AS qtde_devolucao,
 0 AS vlr_devolucao,
 0 AS perc_qtde_estoque,
 0 AS perc_vlr_estoque
FROM dm_produto
LEFT JOIN dm_orcamento_vendas_consolidadas_itens_troca dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda_diaria.dt_emi_pedido BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . "
AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY dm_venda_diaria.cd_filial,
 dm_produto.cd_grupo,
 dm_produto.ds_grupo,
 dm_produto.ds_familia

UNION

SELECT est_produto_cpl_tamanho_prc_filial_estoque.cd_filial,
 dm_produto.cd_grupo,
 dm_produto.ds_grupo,
 dm_produto.ds_familia,
 0:: NUMERIC(14, 4) AS qtde_estoque,
 SUM(est_produto_cpl_tamanho_prc_filial_estoque.qtde_estoque):: NUMERIC(14, 4) AS qtde_estoque_real,
 0:: NUMERIC(14, 2) AS vlr_estoque,
 0:: NUMERIC(14, 4) AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 AVG(est_produto_cpl_tamanho_prc_filial_estoque.vlr_custo_gerenc) AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0:: NUMERIC(14, 4) AS vlr_devolucao,
 0:: NUMERIC(14, 2) AS perc_qtde_estoque,
 0 AS perc_vlr_estoque
FROM dm_produto
INNER JOIN est_produto_cpl_tamanho_prc_filial_estoque
ON est_produto_cpl_tamanho_prc_filial_estoque.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE est_produto_cpl_tamanho_prc_filial_estoque.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }
        if ($parametros['param_estoque'] != "") {
            $SQL .= " AND " . $parametros['param_estoque'] . "";
        }
        $SQL .= " GROUP BY est_produto_cpl_tamanho_prc_filial_estoque.cd_filial,
 dm_produto.cd_grupo,
 dm_produto.ds_grupo,
 dm_produto.ds_familia

UNION

SELECT dm_produto_filial.cd_filial,
 dm_produto.cd_grupo,
 dm_produto.ds_grupo,
 dm_produto.ds_familia,
 0:: NUMERIC(14, 4) AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo)::NUMERIC(14, 2) AS vlr_estoque,
 0:: NUMERIC(14, 4) AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 0 AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0:: NUMERIC(14, 4) AS vlr_devolucao,
 CASE
WHEN tmp.qtde_estoque_total = 0
THEN 0
ELSE ((SUM(dm_produto_filial.qtde_estoque) * 100) / tmp.qtde_estoque_total):: NUMERIC(14, 4)
END AS perc_qtde_estoque,
 CASE
WHEN tmp.qtde_estoque_total = 0
THEN 0
ELSE ((SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo) * 100) / tmp_1.vlr_estoque_total)::NUMERIC(14, 4)
END AS perc_vlr_estoque
FROM ( SELECT SUM(dm_produto_filial.qtde_estoque)::NUMERIC(14, 2) AS qtde_estoque_total
FROM dm_produto
INNER JOIN dm_produto_filial
ON dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")
)
tmp,
 (SELECT SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo)::NUMERIC(14, 2) AS vlr_estoque_total
FROM dm_produto
INNER JOIN dm_produto_filial
ON dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")
)
tmp_1,
 dm_produto_filial,
 dm_produto
WHERE dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
AND dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        if ($parametros['param_estoque'] != "") {
            $SQL .= " AND dm_produto_filial." . $parametros['param_estoque'] . "";
        }
        $SQL .= " GROUP BY dm_produto.cd_grupo,
 dm_produto_filial.cd_filial,
 dm_produto.ds_grupo,
 tmp.qtde_estoque_total,
 tmp_1.vlr_estoque_total,
 dm_produto.ds_familia
)
TMP
GROUP BY TMP.cd_filial,
 TMP.cd_grupo,
 TMP.ds_grupo,
 TMP.ds_familia
)
tbl_union
GROUP BY tbl_union.cd_filial,
 tbl_union.cd_grupo,
 tbl_union.ds_grupo,
 tbl_union.ds_familia
ORDER BY cd_filial,
 qtde_venda DESC";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function vendas_estoque_familia($parametros) {

        $SQL = "";
        $SQL .= "SELECT tbl_union.cd_filial,
 tbl_union.ds_familia,
 SUM(qtde_estoque) AS qtde_estoque,
 SUM(vlr_estoque) AS vlr_estoque,
 SUM(qtde_estoque_real) AS qtde_estoque_real,
 SUM(qtde_venda) - SUM(qtde_devolucao) AS qtde_venda,
 SUM(vlr_venda) - SUM(vlr_devolucao) AS vlr_venda,
 AVG(pcusto):: NUMERIC(14, 2) AS pcusto,
 CASE
WHEN SUM(qtde_venda) <> 0
THEN SUM(pvenda)/SUM(qtde_venda)
ELSE 0
END:: NUMERIC(14, 2) AS pvenda,
 SUM(perc_qtde_estoque)::NUMERIC(14, 2) AS perc_qtde_estoque,
 CASE
WHEN
(
SUM(qtde_venda) - SUM(qtde_devolucao)
)
<> 0
THEN SUM(qtde_estoque_real) / (SUM(qtde_venda) - SUM(qtde_devolucao))
ELSE SUM(qtde_estoque_real)
END AS relacao_estoque
FROM ( SELECT TMP.cd_filial,
 TMP.ds_familia,
 SUM(qtde_estoque) AS qtde_estoque,
 SUM(qtde_estoque_real) AS qtde_estoque_real,
 SUM(vlr_estoque) AS vlr_estoque,
 SUM(qtde_venda) AS qtde_venda,
 SUM(vlr_venda) AS vlr_venda,
 SUM(pvenda) AS pvenda,
 SUM(pcusto) AS PCUSTO,
 SUM(qtde_devolucao) AS qtde_devolucao,
 SUM(vlr_devolucao) AS vlr_devolucao,
 SUM(perc_qtde_estoque) AS perc_qtde_estoque,
 SUM(perc_vlr_estoque) AS perc_vlr_estoque
FROM ( SELECT dm_venda_diaria.cd_filial,
 dm_produto.ds_familia,
 0 AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 0 AS vlr_estoque,
 SUM(COALESCE(dm_venda_diaria.qtde_produto, 0)) AS qtde_venda,
 (SUM(COALESCE(dm_venda_diaria.vl_tot_it - dm_venda_diaria.vl_devol_proporcional, 0)))::NUMERIC(14, 2) AS vlr_venda,
 (SUM(COALESCE(dm_venda_diaria.vl_tot_it, 0))):: NUMERIC(14, 2) AS pvenda,
 NULL:: NUMERIC(14, 2) AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0 AS vlr_devolucao,
 0 AS perc_qtde_estoque,
 0 AS perc_vlr_estoque
FROM dm_produto
LEFT JOIN dm_orcamento_vendas_consolidadas dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda_diaria.dt_vd BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . "
AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY dm_venda_diaria.cd_filial,
 dm_produto.ds_familia

UNION

SELECT dm_venda_diaria.cd_filial,
 dm_produto.ds_familia,
 0 AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 0 AS vlr_estoque,
 0 AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 0:: NUMERIC(14, 2) AS pcusto,
 SUM(COALESCE(dm_venda_diaria.qtde_produto_orig, 0)):: NUMERIC(14, 4) AS qtde_devolucao,
 0 AS vlr_devolucao,
 0 AS perc_qtde_estoque,
 0 AS perc_vlr_estoque
FROM dm_produto
LEFT JOIN dm_orcamento_vendas_consolidadas_itens_troca dm_venda_diaria
ON dm_venda_diaria.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda_diaria.dt_emi_pedido BETWEEN " . $parametros['param_dt_vendas_inicial'] . " AND " . $parametros['param_dt_vendas_final'] . " AND dm_venda_diaria.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        $SQL .= " GROUP BY dm_venda_diaria.cd_filial,
 dm_produto.ds_familia

UNION

SELECT est_produto_cpl_tamanho_prc_filial_estoque.cd_filial,
 dm_produto.ds_familia,
 0:: NUMERIC(14, 4) AS qtde_estoque,
 SUM(est_produto_cpl_tamanho_prc_filial_estoque.qtde_estoque):: NUMERIC(14, 4) AS qtde_estoque_real,
 0:: NUMERIC(14, 2) AS vlr_estoque,
 0:: NUMERIC(14, 4) AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 AVG(est_produto_cpl_tamanho_prc_filial_estoque.vlr_custo_gerenc) AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0:: NUMERIC(14, 4) AS vlr_devolucao,
 0:: NUMERIC(14, 2) AS perc_qtde_estoque,
 0 AS perc_vlr_estoque
FROM dm_produto
INNER JOIN est_produto_cpl_tamanho_prc_filial_estoque
ON est_produto_cpl_tamanho_prc_filial_estoque.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE est_produto_cpl_tamanho_prc_filial_estoque.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        if ($parametros['param_estoque'] != "") {
            $SQL .= " AND " . $parametros['param_estoque'] . "";
        }
        $SQL .= "GROUP BY est_produto_cpl_tamanho_prc_filial_estoque.cd_filial,
 dm_produto.ds_familia

UNION

SELECT dm_produto_filial.cd_filial,
 dm_produto.ds_familia,
 0:: NUMERIC(14, 4) AS qtde_estoque,
 0:: NUMERIC(14, 4) AS qtde_estoque_real,
 SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo)::NUMERIC(14, 2) AS vlr_estoque,
 0:: NUMERIC(14, 4) AS qtde_venda,
 0:: NUMERIC(14, 2) AS vlr_venda,
 0:: NUMERIC(14, 2) AS pvenda,
 0 AS pcusto,
 0:: NUMERIC(14, 4) AS qtde_devolucao,
 0:: NUMERIC(14, 4) AS vlr_devolucao,
 CASE
WHEN tmp.qtde_estoque_total = 0
THEN 0
ELSE ((SUM(dm_produto_filial.qtde_estoque) * 100) / tmp.qtde_estoque_total):: NUMERIC(14, 4)
END AS perc_qtde_estoque,
 CASE
WHEN tmp.qtde_estoque_total = 0
THEN 0
ELSE ((SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo) * 100) / tmp_1.vlr_estoque_total)::NUMERIC(14, 4)
END AS perc_vlr_estoque
FROM ( SELECT SUM(dm_produto_filial.qtde_estoque)::NUMERIC(14, 2) AS qtde_estoque_total
FROM dm_produto
INNER JOIN dm_produto_filial
ON dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")
)
tmp,
 (SELECT SUM(dm_produto_filial.qtde_estoque * dm_produto_filial.vlr_custo)::NUMERIC(14, 2) AS vlr_estoque_total
FROM dm_produto
INNER JOIN dm_produto_filial
ON dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")
)
tmp_1,
 dm_produto_filial,
 dm_produto
WHERE dm_produto_filial.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
AND dm_produto_filial.cd_filial IN (" . $parametros['cd_filial'] . ")";

        if (isset($parametros['param_dt_entrada_inicial_2'])) {
            $SQL .= " AND EXISTS
( SELECT 1
FROM est_produto_cpl_nf_entrada,
 est_produto_cpl_nf_entrada_itens
WHERE est_produto_cpl_nf_entrada.cd_entrada = est_produto_cpl_nf_entrada_itens.cd_entrada
AND est_produto_cpl_nf_entrada.dt_entrada BETWEEN " . $parametros['param_dt_entrada_inicial_2'] . " AND " . $parametros['param_dt_entrada_final_2'] . "
AND dm_produto.cd_cpl_tamanho = est_produto_cpl_nf_entrada_itens.cd_cpl_tamanho
AND est_produto_cpl_nf_entrada_itens.cd_filial IN (" . $parametros['cd_filial'] . ")
)";
        }

        if ($parametros['param_estoque'] != "") {
            $SQL .= " AND dm_produto_filial." . $parametros['param_estoque'] . "";
        }
        $SQL .= " GROUP BY dm_produto_filial.cd_filial,
 tmp.qtde_estoque_total,
 tmp_1.vlr_estoque_total,
 dm_produto.ds_familia
)
TMP
GROUP BY TMP.cd_filial,
 TMP.ds_familia
)
tbl_union
GROUP BY tbl_union.cd_filial,
 tbl_union.ds_familia
ORDER BY cd_filial,
 qtde_venda DESC";


        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function retornaCaixa($parametros) {

        $SQL = "SELECT DISTINCT DS_CX, (cd_filial::varchar || lpad(CD_CX::varchar, 6, '0'))::numeric as cd_cx, cd_filial
FROM PRC_FILIAL_CAIXA
WHERE PRC_FILIAL_CAIXA.CD_EMP = 1
AND PRC_FILIAL_CAIXA.CD_CX > 0
AND PRC_FILIAL_CAIXA.cd_filial IN (" . $parametros . ")
ORDER BY cd_filial, DS_CX";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function fluxo_caixa($parametros) {

        $SQL = "SELECT *,
 COALESCE((
SELECT vlr_abertura
FROM rc_fechamento_cx
WHERE rc_fechamento_cx.cd_emp = tmp1.cd_emp
AND rc_fechamento_cx.cd_cx = tmp1.cd_cx
AND rc_fechamento_cx.cd_fechamento = tmp1.cd_fechamento
AND rc_fechamento_cx.CD_FILIAL = tmp1.cd_filial
AND rc_fechamento_cx.dt_mov = tmp1.dt_mov
AND rc_fechamento_cx.STS_CX IN (1, 3)
), 0) as saldo_caixa,
 COALESCE(( SELECT COUNT(DISTINCT rc_pgto.cd_filial::text
|| rc_pgto.cd_lanc::text
|| rc_pgto.parc::text) AS qtde_baixas
FROM vw_pedido_lanc,
 rc_pgto
WHERE vw_pedido_lanc.cd_emp = rc_pgto.cd_emp
AND vw_pedido_lanc.cd_filial = rc_pgto.cd_filial
AND vw_pedido_lanc.cd_lanc = rc_pgto.cd_lanc
AND dt_lanc = tmp1.dt_mov
AND rc_pgto.sts_pgto = 0
AND vw_pedido_lanc.sts_lanc <> 3
AND rc_pgto.tp_bx IN (0, 2)
AND rc_pgto.parc > 0
AND cd_filial_bx = tmp1.cd_filial
AND cd_cx = tmp1.cd_cx
AND NOT EXISTS
(SELECT cd_filial
FROM rc_pgto_cancel
WHERE rc_pgto.cd_emp = rc_pgto_cancel.cd_emp
AND rc_pgto.cd_filial = rc_pgto_cancel.cd_filial
AND rc_pgto.cd_pgto = rc_pgto_cancel.cd_pgto
AND rc_pgto.cd_lanc = rc_pgto_cancel.cd_lanc
AND rc_pgto.parc = rc_pgto_cancel.parc
)
GROUP BY dt_lanc,
 cd_filial_bx,
 cd_cx), 0) as qtde_baixa,
 COALESCE(( SELECT SUM(troco_rat) AS troco_baixas
FROM vw_pedido_lanc,
 rc_pgto
WHERE vw_pedido_lanc.cd_emp = rc_pgto.cd_emp
AND vw_pedido_lanc.cd_filial = rc_pgto.cd_filial
AND vw_pedido_lanc.cd_lanc = rc_pgto.cd_lanc
AND dt_lanc = tmp1.dt_mov
AND rc_pgto.sts_pgto = 0
AND vw_pedido_lanc.sts_lanc <> 3
AND rc_pgto.tp_bx IN (0, 2)
AND rc_pgto.parc > 0
AND cd_filial_bx = tmp1.cd_filial
AND cd_cx = tmp1.cd_cx
AND NOT EXISTS
(SELECT cd_filial
FROM rc_pgto_cancel
WHERE rc_pgto.cd_emp = rc_pgto_cancel.cd_emp
AND rc_pgto.cd_filial = rc_pgto_cancel.cd_filial
AND rc_pgto.cd_pgto = rc_pgto_cancel.cd_pgto
AND rc_pgto.cd_lanc = rc_pgto_cancel.cd_lanc
AND rc_pgto.parc = rc_pgto_cancel.parc
)
GROUP BY dt_lanc,
 cd_filial_bx,
 cd_cx), 0) as troco_acumulado,
 COALESCE((
SELECT SUM(vlr_abertura)
FROM rc_fechamento_cx
WHERE rc_fechamento_cx.cd_emp = tmp1.cd_emp
AND rc_fechamento_cx.cd_cx = tmp1.cd_cx
AND rc_fechamento_cx.CD_FILIAL = tmp1.cd_filial
AND rc_fechamento_cx.dt_mov = tmp1.dt_mov
AND rc_fechamento_cx.STS_CX IN (0)
), 0) as vlr_abertura,
 COALESCE((
SELECT SUM(vlr_caixa)
FROM rc_fechamento_cx
WHERE rc_fechamento_cx.cd_emp = tmp1.cd_emp
AND rc_fechamento_cx.cd_cx = tmp1.cd_cx
AND rc_fechamento_cx.CD_FILIAL = tmp1.cd_filial
AND rc_fechamento_cx.dt_mov = tmp1.dt_mov
AND rc_fechamento_cx.STS_CX IN (1, 3)
), 0) as vlr_fechamento,
 COALESCE(( SELECT VLR_BANCO
FROM ( SELECT RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_DOCUMENTO,
 RC_FECHAMENTO_CX_RC_DOCUMENTOS.QTDE_DOCUMENTO,
 RC_DOCUMENTOS.PESO_DOCUMENTO,
 RC_FECHAMENTO_CX_RC_DOCUMENTOS.VLR_TOTAL_DOCUMENTO,
 RC_DOCUMENTOS.DS_DOCUMENTO,
 RC_DOCUMENTOS.PERMITE_EDITAR_PESO,
 RC_DOCUMENTOS.ORDEM,
 RC_FECHAMENTO_CX.VLR_BANCO
FROM RC_FECHAMENTO_CX_RC_DOCUMENTOS,
 RC_DOCUMENTOS,
 RC_FECHAMENTO_CX
WHERE RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_DOCUMENTO = RC_DOCUMENTOS.CD_DOCUMENTO
AND RC_FECHAMENTO_CX.CD_FECHAMENTO = RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_FECHAMENTO
AND RC_FECHAMENTO_CX.CD_EMP = RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_EMP
AND RC_FECHAMENTO_CX.CD_FILIAL = RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_FILIAL
AND RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_EMP = tmp1.cd_emp
AND RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_FILIAL = tmp1.cd_filial
AND RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_FECHAMENTO = tmp1.cd_fechamento
)
RSTEMP
GROUP BY vlr_banco), 0) as saldo_banco,
 COALESCE((SELECT SUM(VLR_TOTAL_DOCUMENTO) AS VLR_TOTAL_DOCUMENTO
FROM ( SELECT RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_DOCUMENTO,
 RC_FECHAMENTO_CX_RC_DOCUMENTOS.QTDE_DOCUMENTO,
 RC_DOCUMENTOS.PESO_DOCUMENTO,
 RC_FECHAMENTO_CX_RC_DOCUMENTOS.VLR_TOTAL_DOCUMENTO,
 RC_DOCUMENTOS.DS_DOCUMENTO,
 RC_DOCUMENTOS.PERMITE_EDITAR_PESO,
 RC_DOCUMENTOS.ORDEM,
 RC_FECHAMENTO_CX.VLR_BANCO
FROM RC_FECHAMENTO_CX_RC_DOCUMENTOS,
 RC_DOCUMENTOS,
 RC_FECHAMENTO_CX
WHERE RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_DOCUMENTO = RC_DOCUMENTOS.CD_DOCUMENTO
AND RC_FECHAMENTO_CX.CD_FECHAMENTO = RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_FECHAMENTO
AND RC_FECHAMENTO_CX.CD_EMP = RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_EMP
AND RC_FECHAMENTO_CX.CD_FILIAL = RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_FILIAL
AND RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_EMP = tmp1.cd_emp
AND RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_FILIAL = tmp1.cd_filial
AND RC_FECHAMENTO_CX_RC_DOCUMENTOS.CD_FECHAMENTO = tmp1.cd_fechamento
)
RSTEMP
GROUP BY vlr_banco)) as vlr_total_documento
FROM (SELECT vw_rc_caixa_movimento.cd_filial,
 nm_fant,
 cd_cx,
 dt_mov,
 ds_movimento,
 vlr_mov,
 mov_caixa,
 tp_transacao,
 qtde_itens,
 tp_movimento,
 totaliza_venda,
 vw_rc_caixa_movimento.cd_emp,
 COALESCE((
SELECT MAX(cd_fechamento) AS cd_fechamento
FROM rc_fechamento_cx
WHERE rc_fechamento_cx.cd_emp = vw_rc_caixa_movimento.cd_emp
AND rc_fechamento_cx.cd_cx = vw_rc_caixa_movimento.cd_cx
AND rc_fechamento_cx.CD_FILIAL = vw_rc_caixa_movimento.cd_filial
AND rc_fechamento_cx.dt_mov = vw_rc_caixa_movimento.dt_mov
AND rc_fechamento_cx.STS_CX IN (1, 3)
), 0) as cd_fechamento
FROM vw_rc_caixa_movimento, prc_filial
WHERE dt_mov::DATE BETWEEN " . $parametros['param_dt_inicial'] . " AND " . $parametros['param_dt_final'] . "
AND
(
vw_rc_caixa_movimento.cd_filial::VARCHAR
|| lpad(cd_cx::VARCHAR, 6, '0')
)
::NUMERIC IN (" . $parametros['cd_cx'] . ")
AND vw_rc_caixa_movimento.cd_filial IN (" . $parametros['cd_filial'] . ")
AND vw_rc_caixa_movimento.cd_emp = 1
AND prc_filial.cd_filial = vw_rc_caixa_movimento.cd_filial
AND prc_filial.cd_emp = vw_rc_caixa_movimento.cd_emp
ORDER BY vw_rc_caixa_movimento.cd_filial,
 dt_mov,
 cd_cx,
 mov_caixa = 1 DESC,
 ds_movimento) tmp1";

        $host = $_SESSION['Config']['host'];
        $db = $_SESSION['Config']['databasename'];
        $user = $_SESSION['Config']['user'];
        $password = $_SESSION['Config']['password'];
        $porta = $_SESSION['Config']['porta'];
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        try {
            $dbconn = pg_connect($conn_string);
        } catch (Exception $e) {
            echo "N�o foi possivel conectar � empresa " . $value['nome_empresa'] . "!", $e->getMessage();
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function emails_informativo() {
        $SQL = "SELECT email_usuario from sysapp_controle_envio_email;
";
        $dadosUsuariosEnviaEmail = $this->query($SQL);
        return $dadosUsuariosEnviaEmail;
    }

    public function busca_cd_usuario_informativo($parametroEmail) {
        $SQL = "SELECT cd_usuario from sysapp_config_user where login_usuario IN ('$parametroEmail');
";
        $dadosCdUsuariosEnviaEmail = $this->query($SQL);
        return $dadosCdUsuariosEnviaEmail;
    }

    public function busca_empresa_informativo($parametroCdUsuario) {
        $SQL = "SELECT cd_empresa from sysapp_config_user_empresas where cd_usuario IN ($parametroCdUsuario);
";
        $dadosCdEmpresasEnviaEmail = $this->query($SQL);
        return $dadosCdEmpresasEnviaEmail;
    }

    public function busca_dados_empresa_informativo($parametroCdEmpresa) {
        $SQL = "SELECT * from sysapp_config_empresas where cd_empresa IN ($parametroCdEmpresa);
";
        $dadosEmpresasEnviaEmail = $this->query($SQL);
        return $dadosEmpresasEnviaEmail;
    }

    public function dados_vendas_acumuladas($nome_empresa, $host_param, $db_param, $user_param, $password_param, $porta_param) {

        $data_inicial = "'" . date("Y-m-01") . "'";
        $data_final = "'" . date("Y-m-d") . "'";

        $SQL = "";
        $SQL = " SELECT 0 AS cd_tipo,
 ''::CHARACTER VARYING AS ds_tipo,
 tbl_vendas_metas.cd_filial,
 SUM(vlr_lanc)::NUMERIC(14, 2) AS vlr_lanc,
 SUM(metas):: NUMERIC(14, 2) AS metas,
 SUM(vlr_desconto) AS vlr_desconto,
 SUM(itens) AS itens,
 SUM(vendas)::NUMERIC(14, 2) AS vendas,
 SUM(ABS(vendas_trocas)) AS vendas_trocas,
 SUM(itens_troca) AS itens_troca,
 SUM(vendas) - SUM(vendas_trocas) AS vendas_sem_troca,
 (SUM(itens) - SUM(itens_troca))::NUMERIC(14, 2) AS itens_saldo,
 CASE
WHEN SUM(metas):: NUMERIC(14, 2) = 0 OR SUM(vendas)::NUMERIC(14, 2) = 0
THEN 0
ELSE (SUM(vlr_lanc) / SUM(metas):: NUMERIC(14, 2)) * 100
END AS percent_real,
 CASE
WHEN SUM(itens):: NUMERIC(14, 2) = 0 OR SUM(vlr_lanc)::NUMERIC(14, 2) = 0
THEN 0
WHEN (SUM(itens) - SUM(itens_troca))::NUMERIC(14, 2) = 0
THEN 0
ELSE SUM(vlr_lanc)::NUMERIC(14, 2) / (SUM(itens) - SUM(itens_troca))::NUMERIC(14, 2)
END AS vlr_medio_prod,
 CASE
WHEN SUM(vendas):: NUMERIC(14, 2) = 0 OR SUM(vlr_lanc)::NUMERIC(14, 2) = 0
THEN 0
WHEN (SUM(vendas) - SUM(ABS(vendas_trocas)))::NUMERIC(14, 2) = 0
THEN 0
ELSE SUM(vlr_lanc)::NUMERIC(14, 2) / (SUM(vendas) - SUM(ABS(vendas_trocas)))::NUMERIC(14, 2)
END AS ticket_medio,
 prc_filial.nm_fant
FROM ( SELECT 0 AS cd_tipo,
 ''::CHARACTER VARYING AS ds_tipo,
 cd_usu_cad AS cd_usu,
 '' AS nm_usu,
 cd_filial,
 0 AS vlr_lanc,
 0 AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 0 AS vendas,
 COUNT(*) * -1 AS vendas_trocas,
 SUM(qtde_trocas)::bigint AS itens_troca
FROM ( SELECT orc.cd_filial,
 orc.cd_usu_cad,
 cd_tipo_pgto,
 ds_tipo_pgto,
 SUM(qtde_produto_orig) AS qtde_trocas
FROM dm_orcamento_vendas_consolidadas_itens_troca troca,
 ( SELECT cd_emp,
 cd_usu_cad,
 cd_filial,
 cd_pedido,
 ano,
 mes,
 tp_pagto
FROM dm_orcamento_vendas_consolidadas
WHERE cd_filial IN (select cd_filial from prc_filial)
AND dt_emi_pedido BETWEEN $data_inicial AND $data_final
AND dm_orcamento_vendas_consolidadas.vlr_total_produto::NUMERIC(18, 2) <> dm_orcamento_vendas_consolidadas.vlr_devolucao::NUMERIC(18, 2)
GROUP BY cd_emp,
 cd_usu_cad,
 cd_filial,
 cd_pedido,
 ano,
 mes,
 tp_pagto
)
orc
INNER JOIN glb_tp_pgto
ON orc.cd_emp = glb_tp_pgto.cd_emp
AND orc.tp_pagto = glb_tp_pgto.cd_tipo_pgto
WHERE orc.cd_filial = troca.cd_filial
AND orc.cd_pedido = troca.cd_pedido
AND orc.ano = troca.ano
AND orc.mes = troca.mes
AND troca.cd_filial IN (select cd_filial from prc_filial)
AND troca.dt_emi_pedido BETWEEN $data_inicial AND $data_final
GROUP BY orc.cd_filial,
 cd_tipo_pgto,
 ds_tipo_pgto,
 orc.cd_usu_cad,
 orc.cd_pedido
)
tmp
GROUP BY cd_usu_cad,
 cd_tipo,
 ds_tipo,
 cd_filial

UNION ALL

SELECT 0 AS cd_tipo,
 ''::CHARACTER VARYING AS ds_tipo,
 cd_usu_cad AS cd_usu,
 '' AS nm_usu,
 cd_filial,
 0 AS vlr_lanc,
 0 AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 0 AS vendas,
 COUNT(*) AS vendas_trocas,
 SUM(qtde_trocas)::bigint AS itens_troca
FROM ( SELECT orc.cd_filial,
 orc.cd_usu_cad,
 cd_tipo_pgto,
 ds_tipo_pgto,
 SUM(qtde_produto_orig) AS qtde_trocas
FROM dm_orcamento_vendas_consolidadas_itens_troca troca,
 ( SELECT cd_emp,
 cd_usu_cad,
 cd_filial,
 cd_pedido,
 ano,
 mes,
 tp_pagto
FROM dm_orcamento_vendas_consolidadas
WHERE cd_filial IN (select cd_filial from prc_filial)
AND dt_emi_pedido BETWEEN $data_inicial AND $data_final
AND dm_orcamento_vendas_consolidadas.vlr_total_produto::NUMERIC(18, 2) = dm_orcamento_vendas_consolidadas.vlr_devolucao::NUMERIC(18, 2)
GROUP BY cd_emp,
 cd_usu_cad,
 cd_filial,
 cd_pedido,
 ano,
 mes,
 tp_pagto
)
orc
INNER JOIN glb_tp_pgto
ON orc.cd_emp = glb_tp_pgto.cd_emp
AND orc.tp_pagto = glb_tp_pgto.cd_tipo_pgto
WHERE orc.cd_filial = troca.cd_filial
AND orc.cd_pedido = troca.cd_pedido
AND orc.ano = troca.ano
AND orc.mes = troca.mes
AND troca.cd_filial IN (select cd_filial from prc_filial)
AND troca.dt_emi_pedido BETWEEN $data_inicial AND $data_final
GROUP BY orc.cd_filial,
 cd_tipo_pgto,
 ds_tipo_pgto,
 orc.cd_usu_cad,
 orc.cd_pedido
)
tmp
GROUP BY cd_usu_cad,
 cd_tipo,
 ds_tipo,
 cd_filial

UNION ALL

SELECT cd_tipo_pgto AS cd_tipo,
 ds_tipo_pgto::CHARACTER VARYING AS ds_tipo,
 cd_usu,
 ''::CHARACTER VARYING AS nm_usu,
 cd_filial,
 0 AS vlr_lanc,
 0 AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 COUNT(*) AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca
FROM ( SELECT DISTINCT seg.cd_usu,
 cd_emp,
 tp_pagto,
 dm.cd_filial,
 dm.cd_ped_cred
FROM dm_orcamento_vendas_consolidadas dm
INNER JOIN segu_usu seg
ON seg.cd_usu = cd_usu_cad
WHERE dt_emi_pedido BETWEEN $data_inicial AND $data_final
AND cd_filial IN (select cd_filial from prc_filial)
)
tbl_tmp
INNER JOIN glb_tp_pgto
ON tbl_tmp.cd_emp = glb_tp_pgto.cd_emp
AND tbl_tmp.tp_pagto = glb_tp_pgto.cd_tipo_pgto
GROUP BY cd_usu,
 cd_filial,
 cd_tipo_pgto,
 ds_tipo_pgto

UNION ALL

SELECT cd_tipo_pgto,
 ds_tipo_pgto,
 cd_usu,
 nm_usu,
 ped_vd.cd_filial, ";
        $SQL .= " SUM(vlr_lanc) AS vlr_lanc, ";
        $SQL .= " metas,
 SUM(vlr_desconto) AS vlr_desconto,
 SUM(itens) AS itens,
 vendas,
 vendas_trocas,
 itens_troca
FROM ( SELECT cd_tipo_pgto,
 ds_tipo_pgto,
 cd_usu,
 MAX(nm_usu)::CHARACTER VARYING AS nm_usu,
 cd_filial,
 SUM(vl_tot_it - vl_devol_proporcional) AS vlr_lanc,
 0::NUMERIC AS metas,
 SUM(vlr_desc_it) AS vlr_desconto,
 SUM(qtde_produto) AS itens,
 0 AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca,
 cd_ped_cred,
 cd_emp
FROM ( SELECT 0 AS cd_tipo_pgto,
 ''::CHARACTER VARYING AS ds_tipo_pgto,
 segu_usu.cd_usu,
 segu_usu.nm_usu AS nm_usu,
 dm_venda.cd_filial,
 vl_tot_it,
 vl_devol_proporcional,
 vlr_desc_it,
 qtde_produto,
 cd_ped_cred,
 dm_venda.cd_emp
FROM dm_orcamento_vendas_consolidadas dm_venda
INNER JOIN segu_usu
ON segu_usu.cd_usu = cd_usu_cad
INNER JOIN glb_tp_pgto
ON dm_venda.cd_emp = glb_tp_pgto.cd_emp
AND dm_venda.tp_pagto = glb_tp_pgto.cd_tipo_pgto
WHERE dt_emi_pedido BETWEEN $data_inicial AND $data_final
AND dm_venda.cd_filial IN (select cd_filial from prc_filial)
)
TMP_vlr
GROUP BY cd_usu,
 nm_usu,
 cd_filial,
 cd_tipo_pgto,
 ds_tipo_pgto,
 cd_ped_cred,
 cd_emp
)
TMP,
 ped_vd
WHERE tmp.cd_filial = ped_vd.cd_filial
AND tmp.cd_ped_cred = ped_vd.cd_ped
AND tmp.cd_emp = ped_vd.cd_emp
GROUP BY cd_tipo_pgto,
 ds_tipo_pgto,
 cd_usu,
 nm_usu,
 ped_vd.cd_filial,
 metas,
 vendas,
 vendas_trocas,
 itens_troca

UNION ALL

SELECT 0 AS cd_tipo_pgto,
 '' AS ds_tipo_pgto,
 segu_usu.cd_usu,
 segu_usu.nm_usu,
 vw_meta_vendedor.loja_id AS cd_filial,
 0::NUMERIC AS vlr_lanc,
 SUM(valor_periodo) AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 0 AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca
FROM vw_meta_vendedor,
 glb_pessoa,
 segu_usu,
 segu_usu_glb_pessoa
WHERE segu_usu_glb_pessoa.cd_pessoa = glb_pessoa.cd_pessoa
AND segu_usu_glb_pessoa.cd_usu = segu_usu.cd_usu
AND vw_meta_vendedor.id_vendedor = glb_pessoa.cd_pessoa
AND vw_meta_vendedor.loja_id IN (select cd_filial from prc_filial)
AND vw_meta_vendedor.dt_inicial >= $data_inicial
AND vw_meta_vendedor.dt_final <= $data_final
AND tp_periodo = 1
GROUP BY segu_usu.nm_usu,
 segu_usu.cd_usu,
 loja_id

UNION ALL

SELECT 0 AS cd_tipo_pgto,
 '' AS ds_tipo_pgto,
 segu_usu.cd_usu,
 segu_usu.nm_usu,
 loja_id AS cd_filial,
 0::NUMERIC AS vlr_lanc,
 SUM(valor_periodo) AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 0 AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca
FROM segu_usu_filial_hist_meta_vend his,
 segu_usu,
 segu_usu_glb_pessoa
WHERE segu_usu_glb_pessoa.cd_pessoa = his.id_vendedor
AND segu_usu_glb_pessoa.cd_usu = segu_usu.cd_usu
AND loja_id IN (select cd_filial from prc_filial)
AND dt_cad >= $data_inicial
AND dt_cad <= $data_final
GROUP BY segu_usu.nm_usu,
 segu_usu.cd_usu,
 loja_id

UNION ALL

SELECT cd_tipo,
 ds_tipo,
 tmp_ncc.cd_usu,
 MAX(nm_usu)::CHARACTER VARYING AS nm_usu,
 cd_filial,
 SUM(vl_tot_it) AS vlr_lanc,
 0::NUMERIC AS metas,
 0 AS vlr_desconto,
 SUM(qtde_produto) AS itens,
 0 AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca
FROM ( SELECT 0 AS cd_tipo,
 ''::CHARACTER VARYING AS ds_tipo,
 segu_usu.cd_usu,
 segu_usu.nm_usu AS nm_usu,
 orc.cd_filial AS cd_filial,
 (vlr_credito * (-1)) AS vl_tot_it,
 0 AS vl_devol_proporcional,
 0 AS vlr_desc_it,
 SUM(est_produto_pedido_vendas_cpl_ncc_itens.qtde_produto_orig * (-1)) AS qtde_produto
FROM est_produto_pedido_vendas_cpl_ncc,
 est_produto_pedido_vendas_cpl_ncc_itens,
 rc_pgto_ncc,
 segu_usu,
 est_produto_pedido_vendas_cpl orc
WHERE rc_pgto_ncc.cd_emp = est_produto_pedido_vendas_cpl_ncc.cd_emp
AND rc_pgto_ncc.cd_filial_geracao_ncc = est_produto_pedido_vendas_cpl_ncc.cd_filial
AND rc_pgto_ncc.cd_ctr_ncc = est_produto_pedido_vendas_cpl_ncc.cd_ctr_ncc
AND est_produto_pedido_vendas_cpl_ncc.cd_emp = est_produto_pedido_vendas_cpl_ncc_itens.cd_emp
AND est_produto_pedido_vendas_cpl_ncc.cd_filial = est_produto_pedido_vendas_cpl_ncc_itens.cd_filial
AND est_produto_pedido_vendas_cpl_ncc.cd_ctr_ncc = est_produto_pedido_vendas_cpl_ncc_itens.cd_ctr_ncc
AND rc_pgto_ncc.cd_emp = 1
AND orc.cd_filial IN (select cd_filial from prc_filial)
AND rc_pgto_ncc.dt_cad BETWEEN $data_inicial AND $data_final
AND segu_usu.cd_usu = orc.cd_pessoa_fun
AND est_produto_pedido_vendas_cpl_ncc_itens.cd_filial_orig = orc.cd_filial
AND est_produto_pedido_vendas_cpl_ncc_itens.cd_pedido_orig = orc.cd_pedido
AND est_produto_pedido_vendas_cpl_ncc_itens.mes_orig = orc.mes
AND est_produto_pedido_vendas_cpl_ncc_itens.ano_orig = orc.ano
GROUP BY segu_usu.cd_usu,
 segu_usu.nm_usu,
 orc.cd_filial,
 vlr_credito,
 rc_pgto_ncc.cd_ncc_sequencia
)
tmp_ncc
GROUP BY cd_usu,
 nm_usu,
 cd_filial,
 cd_tipo,
 ds_tipo
)
tbl_vendas_metas,
 prc_filial
WHERE tbl_vendas_metas.cd_filial = prc_filial.cd_filial
GROUP BY tbl_vendas_metas.cd_filial,
 prc_filial.nm_fant
ORDER BY vlr_lanc DESC";

        $host = $host_param;
        $db = $db_param;
        $user = $user_param;
        $password = $password_param;
        $porta = $porta_param;
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        $dbconn = pg_connect($conn_string);

        if (!pg_connect($conn_string)) {
            echo "N�o foi possivel conectar ao Database";
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function dados_vendas_ultimo_dia($nome_empresa, $host_param, $db_param, $user_param, $password_param, $porta_param) {

        $data_inicial = "'" . date("Y-m-d", strtotime("-1 day")) . "'";
        $data_final = "'" . date("Y-m-d", strtotime("-1 day")) . "'";

        $SQL = "";
        $SQL = " SELECT 0 AS cd_tipo,
 ''::CHARACTER VARYING AS ds_tipo,
 tbl_vendas_metas.cd_filial,
 SUM(vlr_lanc)::NUMERIC(14, 2) AS vlr_lanc,
 SUM(metas):: NUMERIC(14, 2) AS metas,
 SUM(vlr_desconto) AS vlr_desconto,
 SUM(itens) AS itens,
 SUM(vendas)::NUMERIC(14, 2) AS vendas,
 SUM(ABS(vendas_trocas)) AS vendas_trocas,
 SUM(itens_troca) AS itens_troca,
 SUM(vendas) - SUM(vendas_trocas) AS vendas_sem_troca,
 (SUM(itens) - SUM(itens_troca))::NUMERIC(14, 2) AS itens_saldo,
 CASE
WHEN SUM(metas):: NUMERIC(14, 2) = 0 OR SUM(vendas)::NUMERIC(14, 2) = 0
THEN 0
ELSE (SUM(vlr_lanc) / SUM(metas):: NUMERIC(14, 2)) * 100
END AS percent_real,
 CASE
WHEN SUM(itens):: NUMERIC(14, 2) = 0 OR SUM(vlr_lanc)::NUMERIC(14, 2) = 0
THEN 0
WHEN (SUM(itens) - SUM(itens_troca))::NUMERIC(14, 2) = 0
THEN 0
ELSE SUM(vlr_lanc)::NUMERIC(14, 2) / (SUM(itens) - SUM(itens_troca))::NUMERIC(14, 2)
END AS vlr_medio_prod,
 CASE
WHEN SUM(vendas):: NUMERIC(14, 2) = 0 OR SUM(vlr_lanc)::NUMERIC(14, 2) = 0
THEN 0
WHEN (SUM(vendas) - SUM(ABS(vendas_trocas)))::NUMERIC(14, 2) = 0
THEN 0
ELSE SUM(vlr_lanc)::NUMERIC(14, 2) / (SUM(vendas) - SUM(ABS(vendas_trocas)))::NUMERIC(14, 2)
END AS ticket_medio,
 prc_filial.nm_fant
FROM ( SELECT 0 AS cd_tipo,
 ''::CHARACTER VARYING AS ds_tipo,
 cd_usu_cad AS cd_usu,
 '' AS nm_usu,
 cd_filial,
 0 AS vlr_lanc,
 0 AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 0 AS vendas,
 COUNT(*) * -1 AS vendas_trocas,
 SUM(qtde_trocas)::bigint AS itens_troca
FROM ( SELECT orc.cd_filial,
 orc.cd_usu_cad,
 cd_tipo_pgto,
 ds_tipo_pgto,
 SUM(qtde_produto_orig) AS qtde_trocas
FROM dm_orcamento_vendas_consolidadas_itens_troca troca,
 ( SELECT cd_emp,
 cd_usu_cad,
 cd_filial,
 cd_pedido,
 ano,
 mes,
 tp_pagto
FROM dm_orcamento_vendas_consolidadas
WHERE cd_filial IN (select cd_filial from prc_filial)
AND dt_emi_pedido BETWEEN $data_inicial AND $data_final
AND dm_orcamento_vendas_consolidadas.vlr_total_produto::NUMERIC(18, 2) <> dm_orcamento_vendas_consolidadas.vlr_devolucao::NUMERIC(18, 2)
GROUP BY cd_emp,
 cd_usu_cad,
 cd_filial,
 cd_pedido,
 ano,
 mes,
 tp_pagto
)
orc
INNER JOIN glb_tp_pgto
ON orc.cd_emp = glb_tp_pgto.cd_emp
AND orc.tp_pagto = glb_tp_pgto.cd_tipo_pgto
WHERE orc.cd_filial = troca.cd_filial
AND orc.cd_pedido = troca.cd_pedido
AND orc.ano = troca.ano
AND orc.mes = troca.mes
AND troca.cd_filial IN (select cd_filial from prc_filial)
AND troca.dt_emi_pedido BETWEEN $data_inicial AND $data_final
GROUP BY orc.cd_filial,
 cd_tipo_pgto,
 ds_tipo_pgto,
 orc.cd_usu_cad,
 orc.cd_pedido
)
tmp
GROUP BY cd_usu_cad,
 cd_tipo,
 ds_tipo,
 cd_filial

UNION ALL

SELECT 0 AS cd_tipo,
 ''::CHARACTER VARYING AS ds_tipo,
 cd_usu_cad AS cd_usu,
 '' AS nm_usu,
 cd_filial,
 0 AS vlr_lanc,
 0 AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 0 AS vendas,
 COUNT(*) AS vendas_trocas,
 SUM(qtde_trocas)::bigint AS itens_troca
FROM ( SELECT orc.cd_filial,
 orc.cd_usu_cad,
 cd_tipo_pgto,
 ds_tipo_pgto,
 SUM(qtde_produto_orig) AS qtde_trocas
FROM dm_orcamento_vendas_consolidadas_itens_troca troca,
 ( SELECT cd_emp,
 cd_usu_cad,
 cd_filial,
 cd_pedido,
 ano,
 mes,
 tp_pagto
FROM dm_orcamento_vendas_consolidadas
WHERE cd_filial IN (select cd_filial from prc_filial)
AND dt_emi_pedido BETWEEN $data_inicial AND $data_final
AND dm_orcamento_vendas_consolidadas.vlr_total_produto::NUMERIC(18, 2) = dm_orcamento_vendas_consolidadas.vlr_devolucao::NUMERIC(18, 2)
GROUP BY cd_emp,
 cd_usu_cad,
 cd_filial,
 cd_pedido,
 ano,
 mes,
 tp_pagto
)
orc
INNER JOIN glb_tp_pgto
ON orc.cd_emp = glb_tp_pgto.cd_emp
AND orc.tp_pagto = glb_tp_pgto.cd_tipo_pgto
WHERE orc.cd_filial = troca.cd_filial
AND orc.cd_pedido = troca.cd_pedido
AND orc.ano = troca.ano
AND orc.mes = troca.mes
AND troca.cd_filial IN (select cd_filial from prc_filial)
AND troca.dt_emi_pedido BETWEEN $data_inicial AND $data_final
GROUP BY orc.cd_filial,
 cd_tipo_pgto,
 ds_tipo_pgto,
 orc.cd_usu_cad,
 orc.cd_pedido
)
tmp
GROUP BY cd_usu_cad,
 cd_tipo,
 ds_tipo,
 cd_filial

UNION ALL

SELECT cd_tipo_pgto AS cd_tipo,
 ds_tipo_pgto::CHARACTER VARYING AS ds_tipo,
 cd_usu,
 ''::CHARACTER VARYING AS nm_usu,
 cd_filial,
 0 AS vlr_lanc,
 0 AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 COUNT(*) AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca
FROM ( SELECT DISTINCT seg.cd_usu,
 cd_emp,
 tp_pagto,
 dm.cd_filial,
 dm.cd_ped_cred
FROM dm_orcamento_vendas_consolidadas dm
INNER JOIN segu_usu seg
ON seg.cd_usu = cd_usu_cad
WHERE dt_emi_pedido BETWEEN $data_inicial AND $data_final
AND cd_filial IN (select cd_filial from prc_filial)
)
tbl_tmp
INNER JOIN glb_tp_pgto
ON tbl_tmp.cd_emp = glb_tp_pgto.cd_emp
AND tbl_tmp.tp_pagto = glb_tp_pgto.cd_tipo_pgto
GROUP BY cd_usu,
 cd_filial,
 cd_tipo_pgto,
 ds_tipo_pgto

UNION ALL

SELECT cd_tipo_pgto,
 ds_tipo_pgto,
 cd_usu,
 nm_usu,
 ped_vd.cd_filial,
 SUM(vlr_lanc) AS vlr_lanc,
 metas,
 SUM(vlr_desconto) AS vlr_desconto,
 SUM(itens) AS itens,
 vendas,
 vendas_trocas,
 itens_troca
FROM ( SELECT cd_tipo_pgto,
 ds_tipo_pgto,
 cd_usu,
 MAX(nm_usu)::CHARACTER VARYING AS nm_usu,
 cd_filial,
 SUM(vl_tot_it - vl_devol_proporcional) AS vlr_lanc,
 0::NUMERIC AS metas,
 SUM(vlr_desc_it) AS vlr_desconto,
 SUM(qtde_produto) AS itens,
 0 AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca,
 cd_ped_cred,
 cd_emp
FROM ( SELECT 0 AS cd_tipo_pgto,
 ''::CHARACTER VARYING AS ds_tipo_pgto,
 segu_usu.cd_usu,
 segu_usu.nm_usu AS nm_usu,
 dm_venda.cd_filial,
 vl_tot_it,
 vl_devol_proporcional,
 vlr_desc_it,
 qtde_produto,
 cd_ped_cred,
 dm_venda.cd_emp
FROM dm_orcamento_vendas_consolidadas dm_venda
INNER JOIN segu_usu
ON segu_usu.cd_usu = cd_usu_cad
INNER JOIN glb_tp_pgto
ON dm_venda.cd_emp = glb_tp_pgto.cd_emp
AND dm_venda.tp_pagto = glb_tp_pgto.cd_tipo_pgto
WHERE dt_emi_pedido BETWEEN $data_inicial AND $data_final
AND dm_venda.cd_filial IN (select cd_filial from prc_filial)
)
TMP_vlr
GROUP BY cd_usu,
 nm_usu,
 cd_filial,
 cd_tipo_pgto,
 ds_tipo_pgto,
 cd_ped_cred,
 cd_emp
)
TMP,
 ped_vd
WHERE tmp.cd_filial = ped_vd.cd_filial
AND tmp.cd_ped_cred = ped_vd.cd_ped
AND tmp.cd_emp = ped_vd.cd_emp
GROUP BY cd_tipo_pgto,
 ds_tipo_pgto,
 cd_usu,
 nm_usu,
 ped_vd.cd_filial,
 metas,
 vendas,
 vendas_trocas,
 itens_troca

UNION ALL

SELECT 0 AS cd_tipo_pgto,
 '' AS ds_tipo_pgto,
 segu_usu.cd_usu,
 segu_usu.nm_usu,
 vw_meta_vendedor.loja_id AS cd_filial,
 0::NUMERIC AS vlr_lanc,
 SUM(valor_periodo) AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 0 AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca
FROM vw_meta_vendedor,
 glb_pessoa,
 segu_usu,
 segu_usu_glb_pessoa
WHERE segu_usu_glb_pessoa.cd_pessoa = glb_pessoa.cd_pessoa
AND segu_usu_glb_pessoa.cd_usu = segu_usu.cd_usu
AND vw_meta_vendedor.id_vendedor = glb_pessoa.cd_pessoa
AND vw_meta_vendedor.loja_id IN (select cd_filial from prc_filial)
AND vw_meta_vendedor.dt_inicial >= $data_inicial
AND vw_meta_vendedor.dt_final <= $data_final
AND tp_periodo = 1
GROUP BY segu_usu.nm_usu,
 segu_usu.cd_usu,
 loja_id

UNION ALL

SELECT 0 AS cd_tipo_pgto,
 '' AS ds_tipo_pgto,
 segu_usu.cd_usu,
 segu_usu.nm_usu,
 loja_id AS cd_filial,
 0::NUMERIC AS vlr_lanc,
 SUM(valor_periodo) AS metas,
 0 AS vlr_desconto,
 0 AS itens,
 0 AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca
FROM segu_usu_filial_hist_meta_vend his,
 segu_usu,
 segu_usu_glb_pessoa
WHERE segu_usu_glb_pessoa.cd_pessoa = his.id_vendedor
AND segu_usu_glb_pessoa.cd_usu = segu_usu.cd_usu
AND loja_id IN (select cd_filial from prc_filial)
AND dt_cad >= $data_inicial
AND dt_cad <= $data_final
GROUP BY segu_usu.nm_usu,
 segu_usu.cd_usu,
 loja_id

UNION ALL

SELECT cd_tipo,
 ds_tipo,
 tmp_ncc.cd_usu,
 MAX(nm_usu)::CHARACTER VARYING AS nm_usu,
 cd_filial,
 SUM(vl_tot_it) AS vlr_lanc,
 0::NUMERIC AS metas,
 0 AS vlr_desconto,
 SUM(qtde_produto) AS itens,
 0 AS vendas,
 0 AS vendas_trocas,
 0 AS itens_troca
FROM ( SELECT 0 AS cd_tipo,
 ''::CHARACTER VARYING AS ds_tipo,
 segu_usu.cd_usu,
 segu_usu.nm_usu AS nm_usu,
 orc.cd_filial AS cd_filial,
 (vlr_credito * (-1)) AS vl_tot_it,
 0 AS vl_devol_proporcional,
 0 AS vlr_desc_it,
 SUM(est_produto_pedido_vendas_cpl_ncc_itens.qtde_produto_orig * (-1)) AS qtde_produto
FROM est_produto_pedido_vendas_cpl_ncc,
 est_produto_pedido_vendas_cpl_ncc_itens,
 rc_pgto_ncc,
 segu_usu,
 est_produto_pedido_vendas_cpl orc
WHERE rc_pgto_ncc.cd_emp = est_produto_pedido_vendas_cpl_ncc.cd_emp
AND rc_pgto_ncc.cd_filial_geracao_ncc = est_produto_pedido_vendas_cpl_ncc.cd_filial
AND rc_pgto_ncc.cd_ctr_ncc = est_produto_pedido_vendas_cpl_ncc.cd_ctr_ncc
AND est_produto_pedido_vendas_cpl_ncc.cd_emp = est_produto_pedido_vendas_cpl_ncc_itens.cd_emp
AND est_produto_pedido_vendas_cpl_ncc.cd_filial = est_produto_pedido_vendas_cpl_ncc_itens.cd_filial
AND est_produto_pedido_vendas_cpl_ncc.cd_ctr_ncc = est_produto_pedido_vendas_cpl_ncc_itens.cd_ctr_ncc
AND rc_pgto_ncc.cd_emp = 1
AND orc.cd_filial IN (select cd_filial from prc_filial)
AND rc_pgto_ncc.dt_cad BETWEEN $data_inicial AND $data_final
AND segu_usu.cd_usu = orc.cd_pessoa_fun
AND est_produto_pedido_vendas_cpl_ncc_itens.cd_filial_orig = orc.cd_filial
AND est_produto_pedido_vendas_cpl_ncc_itens.cd_pedido_orig = orc.cd_pedido
AND est_produto_pedido_vendas_cpl_ncc_itens.mes_orig = orc.mes
AND est_produto_pedido_vendas_cpl_ncc_itens.ano_orig = orc.ano
GROUP BY segu_usu.cd_usu,
 segu_usu.nm_usu,
 orc.cd_filial,
 vlr_credito,
 rc_pgto_ncc.cd_ncc_sequencia
)
tmp_ncc
GROUP BY cd_usu,
 nm_usu,
 cd_filial,
 cd_tipo,
 ds_tipo
)
tbl_vendas_metas,
 prc_filial
WHERE tbl_vendas_metas.cd_filial = prc_filial.cd_filial
GROUP BY tbl_vendas_metas.cd_filial,
 prc_filial.nm_fant
ORDER BY vlr_lanc DESC";

        $host = $host_param;
        $db = $db_param;
        $user = $user_param;
        $password = $password_param;
        $porta = $porta_param;
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        $dbconn = pg_connect($conn_string);

        if (!pg_connect($conn_string)) {
            echo "N�o foi possivel conectar ao Database";
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function dados_vendas_recebimento_acumulado($nome_empresa, $host_param, $db_param, $user_param, $password_param, $porta_param) {

        $data_inicial = "'" . date("Y-m-01") . "'";
        $data_final = "'" . date("Y-m-d") . "'";

        $SQL = "";
        $SQL = "SELECT rc_pgto.cd_emp,
 rc_pgto.cd_filial_bx,
 prc_filial.nm_fant,
 lpad((extract (hour FROM dt_hr_processamento))::VARCHAR, 2, '0')
|| ' �s '
|| lpad((extract (hour FROM dt_hr_processamento)+1)::VARCHAR, 2, '0') AS horario_recebimento,
 COUNT(*) AS quantidade,
 SUM(vlr_pgto - troco_rat)::NUMERIC(14, 2) AS valor_recebido,
 (SUM(vlr_pgto - troco_rat) / COUNT(*))::NUMERIC(14, 2) AS valor_medio
FROM rc_pgto,
 rc_lanc,
 prc_filial
WHERE rc_pgto.cd_emp = rc_lanc.cd_emp
AND rc_pgto.cd_filial_bx = prc_filial.cd_filial
AND rc_pgto.cd_emp = prc_filial.cd_emp
AND rc_pgto.cd_filial = rc_lanc.cd_filial
AND rc_pgto.cd_lanc = rc_lanc.cd_lanc
AND rc_pgto.cd_filial_bx IN (select cd_filial from prc_filial)
AND dt_pag BETWEEN $data_inicial AND $data_final
AND TP_BX IN (0, 2, 4)
AND cd_tipo_pgto = 3
AND sts_lanc <> 3
AND sts_pgto = 0
GROUP BY rc_pgto.cd_emp,
 prc_filial.nm_fant,
 rc_pgto.cd_filial_bx,
 lpad((extract (hour FROM dt_hr_processamento))::VARCHAR, 2, '0')
|| ' �s '
|| lpad((extract (hour FROM dt_hr_processamento)+1)::VARCHAR, 2, '0')
ORDER BY rc_pgto.cd_emp,
 prc_filial.nm_fant,
 rc_pgto.cd_filial_bx,
 lpad((extract (hour FROM dt_hr_processamento))::VARCHAR, 2, '0')
|| ' �s '
|| lpad((extract (hour FROM dt_hr_processamento)+1)::VARCHAR, 2, '0')";


        $host = $host_param;
        $db = $db_param;
        $user = $user_param;
        $password = $password_param;
        $porta = $porta_param;
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        $dbconn = pg_connect($conn_string);

        if (!pg_connect($conn_string)) {
            echo "N�o foi possivel conectar ao Database";
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function dados_vendas_recebimento_ultimo_dia($nome_empresa, $host_param, $db_param, $user_param, $password_param, $porta_param) {

        $data_inicial = "'" . date("Y-m-d", strtotime("-1 day")) . "'";
        $data_final = "'" . date("Y-m-d", strtotime("-1 day")) . "'";

        $SQL = "";
        $SQL = "SELECT rc_pgto.cd_emp,
 rc_pgto.cd_filial_bx,
 prc_filial.nm_fant,
 lpad((extract (hour FROM dt_hr_processamento))::VARCHAR, 2, '0')
|| ' �s '
|| lpad((extract (hour FROM dt_hr_processamento)+1)::VARCHAR, 2, '0') AS horario_recebimento,
 COUNT(*) AS quantidade,
 SUM(vlr_pgto - troco_rat)::NUMERIC(14, 2) AS valor_recebido,
 (SUM(vlr_pgto - troco_rat) / COUNT(*))::NUMERIC(14, 2) AS valor_medio
FROM rc_pgto,
 rc_lanc,
 prc_filial
WHERE rc_pgto.cd_emp = rc_lanc.cd_emp
AND rc_pgto.cd_filial_bx = prc_filial.cd_filial
AND rc_pgto.cd_emp = prc_filial.cd_emp
AND rc_pgto.cd_filial = rc_lanc.cd_filial
AND rc_pgto.cd_lanc = rc_lanc.cd_lanc
AND rc_pgto.cd_filial_bx IN (select cd_filial from prc_filial)
AND dt_pag BETWEEN $data_inicial AND $data_final
AND TP_BX IN (0, 2, 4)
AND cd_tipo_pgto = 3
AND sts_lanc <> 3
AND sts_pgto = 0
GROUP BY rc_pgto.cd_emp,
 prc_filial.nm_fant,
 rc_pgto.cd_filial_bx,
 lpad((extract (hour FROM dt_hr_processamento))::VARCHAR, 2, '0')
|| ' �s '
|| lpad((extract (hour FROM dt_hr_processamento)+1)::VARCHAR, 2, '0')
ORDER BY rc_pgto.cd_emp,
 prc_filial.nm_fant,
 rc_pgto.cd_filial_bx,
 lpad((extract (hour FROM dt_hr_processamento))::VARCHAR, 2, '0')
|| ' �s '
|| lpad((extract (hour FROM dt_hr_processamento)+1)::VARCHAR, 2, '0')";


        $host = $host_param;
        $db = $db_param;
        $user = $user_param;
        $password = $password_param;
        $porta = $porta_param;
        $conn_string = "host = $host port = $porta dbname = $db user = $user password = $password";

        $dbconn = pg_connect($conn_string);

        if (!pg_connect($conn_string)) {
            echo "N�o foi possivel conectar ao Database";
            return false;
        }

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
            $resultadoConsulta = pg_fetch_all($result);

            return $resultadoConsulta;
        }
    }

    public function usuarios_para_permissao() {
        $SQL = "";
        $SQL .= "SELECT cd_usuario, nome_usuario, login_usuario, cd_usu_erp
FROM sysapp_config_user
WHERE NOT EXISTS
( SELECT email_usuario
FROM sysapp_controle_envio_email
WHERE sysapp_config_user.login_usuario = sysapp_controle_envio_email.email_usuario
)";
        return $this->query($SQL);
    }

    public function salva_emails_para_informativo($emails) {
        $SQL = "";
        foreach ($emails as $value) {
            foreach ($value as $email) {
                $SQL .= "INSERT
INTO sysapp_controle_envio_email
(
cd_usuario,
 nome_usuario,
 email_usuario
)
VALUES
(
(CASE
WHEN (SELECT COUNT(cd_usuario)
FROM sysapp_controle_envio_email) = 0 THEN 1
ELSE
(SELECT MAX(cd_usuario)+1
FROM sysapp_controle_envio_email)
END)
,
 (SELECT nome_usuario from sysapp_config_user WHERE login_usuario = '$email'),
 '$email'
)";
                $this->Query($SQL);
                $SQL = "";
            }
        }
    }

    public function remove_emails_do_informativo($emailsPermitidos) {
        $SQL = "";
        foreach ($emailsPermitidos as $value) {
            foreach ($value as $email) {
                $SQL .= "DELETE
FROM sysapp_controle_envio_email
WHERE email_usuario = '$email'";
                $this->Query($SQL);
                $SQL = "";
            }
        }
    }

    public function relatorio_envio($dados) {

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

        $tipo_sms = $dados["Relatorios"]["tipo_sms"];
        $per_ini_envio = $dados["Relatorios"]["per_ini_envio"];
        $per_fim_envio = $dados["Relatorios"]["per_fim_envio"];

        if ($dados["Relatorios"]['clientes'] == 'E') {
            $cd_pessoa = $dados["cliente"];
        } else {
            $cd_pessoa = '';
        }

        $SQL = "";
        $SQL .= "SELECT rc_cobr_sms.cd_emp, rc_cobr_sms.cd_filial, prc_filial.nm_fant AS nm_filial, ";
        $SQL .= " rc_cobr_sms.cd_ped, ";
        $SQL .= " glb_pessoa_cli.cd_pessoa, ";
        $SQL .= " glb_pessoa_cli.nm_fant AS nm_pessoa, ";
        $SQL .= " REPLACE(TO_CHAR(rc_cobr_sms.telefone::bigint, '(99)9999-9999'), ' ', '') AS telefone, ";
        $SQL .= " rc_cobr_sms.tipo_atraso, ";
        $SQL .= " CASE ";
        $SQL .= " WHEN rc_cobr_sms.tipo_atraso = 0 ";
        $SQL .= " THEN 'CLIENTE EM ATRASO' ";
        $SQL .= " WHEN rc_cobr_sms.tipo_atraso = 1 ";
        $SQL .= " THEN 'CLIENTE NEGATIVADO' ";
        $SQL .= " ELSE 'DESCONHECIDO' ";
        $SQL .= " END AS ds_tipo_atraso, ";
        $SQL .= " TO_CHAR(rc_cobr_sms.dt_envio, 'dd/MM/YYYY') AS dt_envio, ";
        $SQL .= " glb_cobr_periodos.atraso_inicial ";
        $SQL .= " || '/' ";
        $SQL .= " || glb_cobr_periodos.atraso_final AS periodo ";
        $SQL .= "FROM rc_cobr_sms, ";
        $SQL .= " glb_pessoa_cli, ";
        $SQL .= " glb_cobr_periodos, ";
        $SQL .= " prc_filial ";
        $SQL .= "WHERE rc_cobr_sms.cd_emp = glb_pessoa_cli.cd_emp ";
        $SQL .= "AND rc_cobr_sms.cd_pessoa = glb_pessoa_cli.cd_pessoa ";
        $SQL .= "AND rc_cobr_sms.cd_periodo = glb_cobr_periodos.cd_ctr ";
        $SQL .= "AND rc_cobr_sms.cd_emp = '1' ";
//            $SQL .= "AND rc_cobr_sms.cd_emp = '$cd_emp' ";
        $SQL .= "AND prc_filial.cd_emp = rc_cobr_sms.cd_emp ";
        $SQL .= "AND prc_filial.cd_filial = rc_cobr_sms.cd_filial ";

        if (empty($per_ini_envio)) {
            $per_ini_envio = '1990-01-01';
        } else {
            $per_ini_envio = $funcionalidades->formatarDataBd($per_ini_envio);
        }
        if ($per_fim_envio == null) {
            $per_fim_envio = date("Y-m-d");
        } else {
            $per_fim_envio = $funcionalidades->formatarDataBd($per_fim_envio);
        }
        $SQL .= " AND rc_cobr_sms.dt_envio BETWEEN '$per_ini_envio' AND '$per_fim_envio' ";

        if (isset($dados['Relatorios']['filial'])) {
            $cod_filiais = '';
            foreach ($dados['Relatorios']['filial'] as $value) {
                $cod_filiais .= ", " . $value;
            }
            $cod_filiais = substr($cod_filiais, 1);
            $SQL .= " AND rc_cobr_sms.cd_filial IN ($cod_filiais) ";
        }

        if ($tipo_sms != 'T') {
            $SQL .= "AND rc_cobr_sms.tipo_atraso = $tipo_sms ";
        }

        if ($cd_pessoa != null) {
            $SQL .= "AND rc_cobr_sms.cd_pessoa = '$cd_pessoa'";
        }

        $SQL .= " ORDER BY rc_cobr_sms.cd_filial, rc_cobr_sms.tipo_atraso, rc_cobr_sms.dt_envio, glb_cobr_periodos.atraso_inicial, glb_cobr_periodos.atraso_final, glb_pessoa_cli.nm_fant, rc_cobr_sms.cd_ped";
        $SQL .= " LIMIT 1000"; // RETIRAR;
//        die($SQL);
        return $this->query($SQL);
    }

    public function relatorio_retorno($dados) {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

        $tipo_sms = $dados["Relatorios"]["tipo_sms"];

        $per_ini_envio = $dados["Relatorios"]["per_ini_envio"];
        $per_fim_envio = $dados["Relatorios"]["per_fim_envio"];

        $per_ini_retorno = $dados["Relatorios"]["per_ini_retorno"];
        $per_fim_retorno = $dados["Relatorios"]["per_fim_retorno"];

        if (empty($per_ini_envio)) {
            $per_ini_envio = '1990-01-01';
        } else {
            $per_ini_envio = $funcionalidades->formatarDataBd($per_ini_envio);
        }
        if ($per_fim_envio == null) {
            $per_fim_envio = date("Y-m-d");
        } else {
            $per_fim_envio = $funcionalidades->formatarDataBd($per_fim_envio);
        }

        if (empty($per_ini_retorno)) {
            $per_ini_retorno = '1990-01-01';
        } else {
            $per_ini_retorno = $funcionalidades->formatarDataBd($per_ini_retorno);
        }
        if ($per_fim_retorno == null) {
            $per_fim_retorno = date("Y-m-d");
        } else {
            $per_fim_retorno = $funcionalidades->formatarDataBd($per_fim_retorno);
        }

        if ($dados["Relatorios"]['clientes'] == 'E') {
            $cd_pessoa = $dados["cliente"];
        } else {
            $cd_pessoa = '';
        }

        $SQL = '';
        $SQL .= "SELECT rc_cobr_sms.cd_filial, ";
        $SQL .= " prc_filial.nm_fant as nm_filial, ";
        $SQL .= " glb_pessoa_cli.cd_pessoa, ";
        $SQL .= " rc_cobr_sms.tipo_atraso, ";
        $SQL .= " rc_cobr_sms.tipo_atraso, ";
        $SQL .= " CASE ";
        $SQL .= " WHEN rc_cobr_sms.tipo_atraso = 0 ";
        $SQL .= " THEN 'CLIENTE EM ATRASO' ";
        $SQL .= " WHEN rc_cobr_sms.tipo_atraso = 1 ";
        $SQL .= " THEN 'CLIENTE NEGATIVADO' ";
        $SQL .= " ELSE 'DESCONHECIDO' ";
        $SQL .= " END AS ds_tipo_atraso, ";
        $SQL .= " glb_pessoa_cli.nm_fant, ";
        $SQL .= " SUBSTR(dt_envio::text, 0, 11) AS dt_envio_sms, ";
        $SQL .= " SUBSTR(dt_envio::text, 12, 8) AS hr_envio_sms, ";
        $SQL .= " SUM(rc_pgto.vlr_pgto - rc_pgto.vlr_jur + rc_pgto.vlr_desc ) AS vlr_pgto, ";
        $SQL .= " dt_pag AS dt_pagamento, ";
        $SQL .= " rc_lanc.cd_lanc AS nr_contrato ";
        $SQL .= "FROM rc_cobr_sms, ";
        $SQL .= " glb_pessoa_cli, ";
        $SQL .= " rc_lanc, ";
        $SQL .= " rc_pgto, ";
        $SQL .= " prc_filial ";
        $SQL .= "WHERE rc_cobr_sms.cd_emp = glb_pessoa_cli.cd_emp ";
        $SQL .= "AND rc_cobr_sms.cd_pessoa = glb_pessoa_cli.cd_pessoa ";
        $SQL .= "AND rc_lanc.cd_emp = rc_pgto.cd_emp ";
        $SQL .= "AND rc_cobr_sms.cd_filial = prc_filial.cd_filial ";
        $SQL .= "AND rc_lanc.cd_filial = rc_pgto.cd_filial ";
        $SQL .= "AND rc_lanc.cd_lanc = rc_pgto.cd_lanc ";
        $SQL .= "AND rc_pgto.sts_pgto = 0 ";
        $SQL .= "AND rc_lanc.cd_pessoa = glb_pessoa_cli.cd_pessoa ";

        if (isset($dados['Relatorios']['filial'])) {
            $cod_filiais = '';
            foreach ($dados['Relatorios']['filial'] as $value) {
                $cod_filiais .= ", " . $value;
            }
            $cod_filiais = substr($cod_filiais, 1);
            $SQL .= " AND rc_cobr_sms.cd_filial IN ($cod_filiais) ";
        }


        if ($tipo_sms != 'T') {
            $SQL .= "AND rc_cobr_sms.tipo_atraso = $tipo_sms ";
        }


        if ($cd_pessoa != null) {
            $SQL .= " AND glb_pessoa_cli.cd_pessoa = '$cd_pessoa' ";
        }
        $SQL .= "AND rc_cobr_sms.cd_emp = '1' ";
        $SQL .= "AND rc_cobr_sms.dt_envio BETWEEN '$per_ini_envio' AND '$per_fim_envio' ";
        $SQL .= "AND dt_pag BETWEEN '$per_ini_retorno' AND '$per_fim_retorno' ";
        $SQL .= "GROUP BY rc_cobr_sms.cd_filial, ";
        $SQL .= " prc_filial.nm_fant, ";
        $SQL .= " glb_pessoa_cli.cd_pessoa, ";
        $SQL .= " rc_cobr_sms.tipo_atraso, ";
        $SQL .= " glb_pessoa_cli.nm_fant, ";
        $SQL .= " dt_envio, ";
        $SQL .= " dt_pag, ";
        $SQL .= " rc_lanc.cd_lanc ";
        $SQL .= "ORDER BY rc_cobr_sms.cd_filial, ";
        $SQL .= " dt_envio, ";
        $SQL .= " glb_pessoa_cli.cd_pessoa, ";
        $SQL .= " dt_pag, ";
        $SQL .= " rc_lanc.cd_lanc";
//        $SQL .= " LIMIT 10000"; // RETIRAR;
//        die($SQL);
        return $this->query($SQL);
    }

    public function sms_valor_sintetico($dados) {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_ini_envio'])) {
            $data_ini = '1900-01-01';
        } else {
            $data_ini = $funcionalidades->formatarDataBd($dados['Relatorios']['per_ini_envio']);
        }
        if (empty($dados['Relatorios']['per_fim_envio'])) {
            $data_fim = date('Y-m-d');
        } else {
            $data_fim = $funcionalidades->formatarDataBd($dados['Relatorios']['per_fim_envio']);
        }
        $SQL = "";
        $SQL .= "SELECT cd_campanha, ";
        $SQL .= " COUNT(*) AS qtde_clientes, ";
        $SQL .= " SUM(valor_sms) AS vlr_total_sms ";
        $SQL .= "FROM vw_campanha_sms ";
        $SQL .= "WHERE dt_hr_envio BETWEEN '" . $data_ini . "' AND '" . $data_fim . "' ";
        if (isset($dados['Relatorios']['campanhas'])) {
            $campanhas = '';
            foreach ($dados['Relatorios']['campanhas'] as $key => $value) {
                $campanhas .= ',' . $value;
            }
            $campanhas = substr($campanhas, 1);
            $SQL .= "AND cd_campanha IN($campanhas) ";
        }
        if ($dados['Relatorios']['clientes'] == 'E') {
            $SQL .= "AND cd_pessoa = '{$dados['cliente']}' ";
        }
        $SQL .= "GROUP BY cd_campanha";
        return $this->query($SQL);
    }

    public function sms_valor_analitico($dados) {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_ini_envio'])) {
            $data_ini = '1900-01-01';
        } else {
            $data_ini = $funcionalidades->formatarDataBd($dados['Relatorios']['per_ini_envio']);
        }
        if (empty($dados['Relatorios']['per_fim_envio'])) {
            $data_fim = date('Y-m-d');
        } else {
            $data_fim = $funcionalidades->formatarDataBd($dados['Relatorios']['per_fim_envio']);
        }

        $SQL = "";
        $SQL .= "SELECT nm_campanha, ";
        $SQL .= " dt_hr_envio, ";
        $SQL .= " vw_campanha_sms.cd_pessoa, ";
        $SQL .= " glb_pessoa.nm_pessoa, ";
        $SQL .= " telefone, ";
        $SQL .= " valor_sms ";
        $SQL .= "FROM vw_campanha_sms ";
        $SQL .= " INNER JOIN glb_pessoa ";
        $SQL .= " ON vw_campanha_sms.cd_pessoa = glb_pessoa.cd_pessoa ";
        $SQL .= "WHERE dt_hr_envio BETWEEN '" . $data_ini . "' AND '" . $data_fim . "' ";
        if (isset($dados['Relatorios']['campanhas'])) {
            $campanhas = '';
            foreach ($dados['Relatorios']['campanhas'] as $key => $value) {
                $campanhas .= ',' . $value;
            }
            $campanhas = substr($campanhas, 1);
            $SQL .= "AND cd_campanha IN($campanhas) ";
        }
        if ($dados['Relatorios']['clientes'] == 'E') {
            $SQL .= "AND cd_pessoa = '{$dados['cliente']}' ";
        }
        $SQL .= "ORDER BY dt_hr_envio, nm_pessoa";
        return $this->query($SQL);
    }

    public function campanhas() {
        return $this->query('select * from vw_campanhas');
    }

    public function acompanhamento_ecommerce($dados, $tipoRequisicao) {

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

        $SQL = '';
        $SQL .= "select * from vw_vtex_status where 1 = 1 ";
        if ($tipoRequisicao == 'ajax') {
            foreach ($dados as $key => $value) {
                if ($value != null) {
                    if ($key == 'nm_cliente' || $key == 'status_erp') {
                        $SQL .= " AND upper($key) LIKE upper('%$value%') ";
                    } else if ($key == 'selected_sla' || $key == 'status_pedido' || $key == 'anti_fraude') {
                        $value = substr($value, 1);
                        $SQL .= " AND $key IN ($value) ";
                    } else if ($key == 'data_criacao') {
                        $value = $funcionalidades->formatarDataBd($value);
                        $SQL .= " AND $key = '$value' ";
                    } else {
                        $SQL .= " AND $key = '$value' ";
                    }
                }
            }
        }
        $SQL .= " order by data_criacao DESC, status, selected_sla";

//        die($SQL);
//$SQL .= " order by selected_sla, data_criacao DESC, status || 'z'";
        return $this->query($SQL);
    }

    public function qtd_pedido() {
        return $this->query("select status_pedido, count(status_pedido) from vw_vtex_status group by status_pedido");
    }

    public function sku_pendente() {
        return $this->query("select count(*) as qtde_sku_pendente from est_produto_cpl_tamanho_ecommerce where dt_hr_cad = '1900-01-01'");
    }

    public function sku_pendente_estoque() {
        return $this->query("select count(*) as qtde_sku_pendente_estoque from estoque_pendencia_exportacao_ecommerce where dt_hr_exportacao::date = '1900-01-01'");
    }

    public function sku_pendente_preco() {
        return $this->query("select count(*) as qtde_sku_pendente_preco from preco_pendencia_exportacao_ecommerce where dt_hr_exportacao::date = '1900-01-01'");
    }

    public function sku_sincronizado() {
        return $this->query("select coalesce(count(*), 0) as qtde_sku_sincronizado_hoje, coalesce(min(dt_hr_cad)::time::text, '-') as primeira_sincronizacao_hoje, coalesce(max(dt_hr_cad)::time::text, '-') as ultima_sincronizacao_hoje from est_produto_cpl_tamanho_ecommerce where dt_hr_cad::date = current_date");
    }

    public function sku_sincronizado_estoque() {
        return $this->query("select coalesce(count(*), 0) as qtde_sku_sincronizado_estoque_hoje, coalesce(min(dt_hr_exportacao)::time::text, '-') as primeira_sincronizacao_estoque_hoje, coalesce(max(dt_hr_exportacao)::time::text, '-') as ultima_sincronizacao_estoque_hoje from estoque_pendencia_exportacao_ecommerce where dt_hr_exportacao::date = current_date");
    }

    public function sku_sincronizado_preco() {
        return $this->query("select coalesce(count(*), 0) as qtde_sku_sincronizado_preco_hoje, coalesce(min(dt_hr_exportacao)::time::text, '-') as primeira_sincronizacao_preco_hoje, coalesce(max(dt_hr_exportacao)::time::text, '-') as ultima_sincronizacao_preco_hoje from preco_pendencia_exportacao_ecommerce where dt_hr_exportacao::date = current_date");
    }

    public function acompanhamento_ecommerce_cancelados($dados) {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_inicio'])) {
            $data_ini = '1900-01-01';
        } else {
            $data_ini = $funcionalidades->formatarDataBd($dados['Relatorios']['per_inicio']);
        }
        if (empty($dados['Relatorios']['per_fim'])) {
            $data_fim = date('Y-m-d');
        } else {
            $data_fim = $funcionalidades->formatarDataBd($dados['Relatorios']['per_fim']);
        }
        $SQL = '';
        $SQL .= "select * from vw_exportacao_ecommerce_pedidos_cancelados where creation_date BETWEEN '$data_ini' AND '$data_fim'";
        if (!empty($dados['Relatorios']['numero_ecommerce'])) {
            $SQL .= " AND order_id LIKE '%" . $dados['Relatorios']['numero_ecommerce'] . "%'";
        }
        return $this->query($SQL);
    }

    public function acompanhamento_ecommerce_baixa($dados) {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_inicio'])) {
            $data_ini = '1900-01-01';
        } else {
            $data_ini = $funcionalidades->formatarDataBd($dados['Relatorios']['per_inicio']);
        }
        if (empty($dados['Relatorios']['per_fim'])) {
            $data_fim = date('Y-m-d');
        } else {
            $data_fim = $funcionalidades->formatarDataBd($dados['Relatorios']['per_fim']);
        }
        $SQL = '';
        $SQL .= "select * from vw_ecommerce_pedido_importacao_log where dt_cad BETWEEN '$data_ini' AND '$data_fim'";
        if (!empty($dados['Relatorios']['numero_ecommerce'])) {
            $SQL .= " AND order_id LIKE '%" . $dados['Relatorios']['numero_ecommerce'] . "%'";
        }
        return $this->query($SQL);
    }

    public function detalhe_pedido_ecommerce($cd_sequencia_pedido) {
        return $this->query("select * from vw_vtex_status_it where cd_sequencia_pedido = '$cd_sequencia_pedido'");
    }

    public function gravar_tracking_number($codigo, $codigo_interno) {
        return $this->query("update ecommerce_pedido_items_est_produto_cpl_pre_fatura_cliente set tracking_number = '$codigo' where cd_sequencia_pedido = $codigo_interno");
    }

    public function gravar_anti_fraude($anti_fraude, $codigo_interno) {
        return $this->query("update ecommerce_pedido_cabecalho set anti_fraude = '$anti_fraude' where cd_sequencia_pedido = $codigo_interno");
    }

    public function atendimento_por_pergunta($data_in, $data_fim, $questionario) {
        $SQL = "";
        $SQL .= "SELECT cd_questionario, ";
        $SQL .= " ds_questionario, ";
        $SQL .= " cd_pergunta, ";
        $SQL .= " ds_pergunta, ";
        $SQL .= " cd_pergunta_cpl, ";
        $SQL .= " ds_pergunta_cpl, ";
        $SQL .= " SUM(qtde_resposta) AS qtde_resposta ";
        $SQL .= "FROM vw_questionario_avaliacao_atendimento ";
        $SQL .= "WHERE ( ";
        $SQL .= " /* CRITERIO FIXO */ ";
        $SQL .= " dt_atendimento = '1900-01-01' ";
        $SQL .= " OR dt_atendimento BETWEEN '$data_in' AND '$data_fim' ";
        $SQL .= " ) ";
        $SQL .= "AND cd_questionario = $questionario ";
        $SQL .= "GROUP BY cd_questionario, ";
        $SQL .= " ds_questionario, ";
        $SQL .= " cd_pergunta, ";
        $SQL .= " ds_pergunta, ";
        $SQL .= " cd_pergunta_cpl, ";
        $SQL .= " ds_pergunta_cpl ";
        $SQL .= "ORDER BY ds_questionario, ";
        $SQL .= " ds_pergunta, ";
        $SQL .= " ds_pergunta_cpl";

        return $this->query($SQL);
    }

    public function inadimplencia($dados) {

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_ini_pesquisas'])) {
            $per_ini_pesquisas = date('Y-m-d');
        } else {
            $per_ini_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_ini_pesquisas']);
        }
        if (empty($dados['Relatorios']['per_fim_pesquisas'])) {
            $per_fim_pesquisas = date('Y-m-d');
        } else {
            $per_fim_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_fim_pesquisas']);
        }
        $SQL .="SELECT extract('year' FROM rc_lanc_cpl.dt_vencto) AS ano, ";
        $SQL .="glb_cargo.ds_cargo, ";
        $SQL .="SUM(vlr_parc) AS valor_total, ";
        $SQL .="SUM(sld_parc) AS valor_em_aberto, ";
        $SQL .="(SUM(sld_parc)/SUM(vlr_parc)*100.00)::numeric(14, 2) AS percentual_inadimplencia, ";
        $SQL .="COUNT(*) AS quantidade_total, ";
        $SQL .=" SUM(case sld_parc when 0 then 0 else 1 end) AS quantidade_em_aberto, ";
        $SQL .="((SUM(case sld_parc when 0 then 0 else 1 end)::numeric / COUNT(*)::numeric) * 100.00)::numeric(14, 2) as percentual_qtde_em_aberto ";
        $SQL .="FROM vw_pedido_lanc, ";
        $SQL .="rc_lanc_cpl, ";
        $SQL .="glb_pessoa_trabalho, ";
        $SQL .="glb_cargo ";
        $SQL .="WHERE vw_pedido_lanc.cd_emp = rc_lanc_cpl.cd_emp ";
        $SQL .="AND vw_pedido_lanc.cd_filial = rc_lanc_cpl.cd_filial ";
        $SQL .="AND vw_pedido_lanc.cd_lanc = rc_lanc_cpl.cd_lanc ";
        $SQL .="AND vw_pedido_lanc.cd_lanc = rc_lanc_cpl.cd_lanc ";
        $SQL .="AND vw_pedido_lanc.cd_emp = glb_pessoa_trabalho.cd_emp ";
        $SQL .="AND vw_pedido_lanc.cd_pessoa = glb_pessoa_trabalho.cd_pessoa ";
        $SQL .="AND glb_pessoa_trabalho.cd_cargo = glb_cargo.cd_cargo ";
        $SQL .="AND cd_tipo_pgto = 3 ";
        $SQL .="AND sts_lanc <> 3 ";
        $SQL .="AND sts_parc NOT IN (3)";
        $SQL .="AND vw_pedido_lanc.cd_emp = 1 ";

        if (isset($dados['Relatorios']['filiais'])) {
            $filial = '';
            foreach ($dados['Relatorios']['filiais'] as $value) {
                $filial .= ", " . $value;
            }
            $filial = substr($filial, 1);
            $SQL .= " AND vw_pedido_lanc.cd_filial IN ($filial) ";
        }
        if (isset($dados['Relatorios']['cargo'])) {
            $cargos = '';
            foreach ($dados['Relatorios']['cargo'] as $value) {
                $cargos .= ", " . $value;
            }
            $cargos = substr($cargos, 1);
            $SQL .= " AND glb_pessoa_trabalho.cd_cargo IN ($cargos) ";
        }
        $SQL .="AND dt_vencto BETWEEN '$per_ini_pesquisas' AND '$per_fim_pesquisas' ";
        $SQL .="GROUP BY extract('year' FROM rc_lanc_cpl.dt_vencto), ";
        $SQL .="glb_cargo.ds_cargo ";
        $SQL .="having ";
        $SQL .= "SUM(sld_parc) > 0 ";
        $SQL .="ORDER BY extract('year' FROM rc_lanc_cpl.dt_vencto), (ds_cargo), ";
        $SQL .="(SUM(sld_parc)/SUM(vlr_parc)*100.00)::numeric(14, 2) desc, ";
        $SQL .="glb_cargo.ds_cargo";
        return $this->query($SQL);
    }

    public function descricao_atendimento($dados) {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_ini_pesquisas'])) {
            $per_ini_pesquisas = date('Y-m-d');
        } else {
            $per_ini_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_ini_pesquisas']);
        }
        if (empty($dados['Relatorios']['per_fim_pesquisas'])) {
            $per_fim_pesquisas = date('Y-m-d');
        } else {
            $per_fim_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_fim_pesquisas']);
        }
        if ($dados["Relatorios"]['clientes'] == 'E') {
            $cd_pessoa = $dados["cliente"];
        } else {
            $cd_pessoa = '';
        }

        $SQL .="select ";
        $SQL .="cd_questionario, ";
        $SQL .="ds_questionario, ";
        $SQL .="cd_pessoa, ";
        $SQL .="nm_pessoa, ";
        $SQL .="dt_cad, ";
        $SQL .="to_char(dt_cad, 'DD-MM-YYYY') as data_cadastro, ";
        $SQL .="status_atendimento, ";
        $SQL .="protocolo, ";
        $SQL .="cpf_cgc, ";
        $SQL .="ds_historico, ";
        $SQL .="hora_inicio, ";
        $SQL .="hora_fim ";
        $SQL .="from vw_glb_pesquisa_resposta ";
        $SQL .="WHERE ";
        $SQL .="dt_cad BETWEEN '$per_ini_pesquisas' and '$per_fim_pesquisas'";

        if (isset($dados['Relatorios']['pesquisa'])) {
            $pesquisa = '';
            foreach ($dados['Relatorios']['pesquisa'] as $key => $value) {
                $pesquisa .= ',' . $value;
            }
            $pesquisa = substr($pesquisa, 1);
            $SQL .= "AND cd_questionario IN($pesquisa) ";
        }
        if ($cd_pessoa != null) {
            $SQL .= " AND cd_pessoa IN ($cd_pessoa) ";
        }
        $SQL .="ORDER BY ds_questionario, ";
        $SQL .="dt_cad, ";
        $SQL .="nm_pessoa";
        return $this->query($SQL);
    }

    public function acompanhamento_tempo_crediario($dados) {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_ini_pesquisas'])) {
            $per_ini_pesquisas = date('Y-m-d');
        } else {
            $per_ini_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_ini_pesquisas']);
        }
        if (empty($dados['Relatorios']['per_fim_pesquisas'])) {
            $per_fim_pesquisas = date('Y-m-d');
        } else {
            $per_fim_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_fim_pesquisas']);
        }
        if ($dados["Relatorios"]['clientes'] == 'E') {
            $cd_pessoa = $dados["cliente"];
        } else {
            $cd_pessoa = '';
        }

        $SQL.="select ";
        $SQL.="dt_emi_pedido, ";
        $SQL.="data_pedido, ";
        $SQL.="cd_pessoa, ";
        $SQL.="nm_pessoa, ";
        $SQL.="cd_filial, ";
        $SQL.="tempo_espera as tempo_espera, ";
        $SQL.="tempo_lancamento as tempo_lancamento, ";
        $SQL.="segundos_espera, ";
        $SQL.="segundos_lancamento ";
        $SQL .="FROM vw_acompanhamento_tempo_crediario ";
        $SQL .="WHERE ";
        $SQL .=" cd_pessoa >3 ";

        if (isset($dados['Relatorios']['filiais'])) {
            $filial = '';
            foreach ($dados['Relatorios']['filiais'] as $value) {
                $filial .= ", " . $value;
            }
            $filial = substr($filial, 1);
            $SQL .= " AND cd_filial IN ($filial) ";
        }
        if ($cd_pessoa != null) {
            $SQL .= " AND cd_pessoa = '$cd_pessoa' ";
        }
        $SQL .="AND dt_emi_pedido BETWEEN '$per_ini_pesquisas' and '$per_fim_pesquisas'";
        $SQL .="ORDER BY cd_filial, dt_emi_pedido, nm_pessoa ";
        return $this->query($SQL);
    }

    public function acompanhamento_tempo_crediario_grafico($dados) {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_ini_pesquisas'])) {
            $per_ini_pesquisas = date('Y-m-d');
        } else {
            $per_ini_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_ini_pesquisas']);
        }
        if (empty($dados['Relatorios']['per_fim_pesquisas'])) {
            $per_fim_pesquisas = date('Y-m-d');
        } else {
            $per_fim_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_fim_pesquisas']);
        }
        $SQL .="select ";
        $SQL .="cd_filial, ";
        $SQL .="dt_emi_pedido, ";
        $SQL .="sum(tempo_espera) as tempo_espera, ";
        $SQL .="sum(tempo_lancamento) as tempo_lancamento, ";
        $SQL .="(substr(sum(tempo_espera )::text, 1, 2)::integer + substr(sum(tempo_espera) ::text, 4, 2)::numeric / 60)::numeric(10, 2) as tempo_espera_horas, ";
        $SQL .="(substr(sum(tempo_lancamento)::text, 1, 2)::integer + substr(sum(tempo_lancamento)::text, 4, 2)::numeric / 60)::numeric(10, 2) as tempo_lancamento_horas ";
        $SQL .="From ";
        $SQL .="vw_acompanhamento_tempo_crediario ";
        $SQL .="WHERE ";
        $SQL .="cd_pessoa >3 ";
        if (isset($dados['Relatorios']['filiais'])) {
            $filial = '';
            foreach ($dados['Relatorios']['filiais'] as $value) {
                $filial .= ", " . $value;
            }
            $filial = substr($filial, 1);
            $SQL .= " AND vw_acompanhamento_tempo_crediario.cd_filial IN ($filial) ";
        }
        $SQL .="AND vw_acompanhamento_tempo_crediario.dt_emi_pedido BETWEEN '$per_ini_pesquisas' and '$per_fim_pesquisas'";
        $SQL .="Group by ";
        $SQL .=" dt_emi_pedido, ";
        $SQL .=" data_pedido, ";
        $SQL .=" cd_filial ";
        $SQL .="ORDER BY ";
        $SQL .=" dt_emi_pedido, ";
        $SQL .=" cd_filial ";
        return $this->query($SQL);
    }

    public function atendimento_aniversariante($dados) {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_ini_pesquisas'])) {
            $per_ini_pesquisas = date('Y-m-d');
        } else {
            $per_ini_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_ini_pesquisas']);
        }
        if (empty($dados['Relatorios']['per_fim_pesquisas'])) {
            $per_fim_pesquisas = date('Y-m-d');
        } else {
            $per_fim_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_fim_pesquisas']);
        }
        if ($dados["Relatorios"]['clientes'] == 'E') {
            $cd_pessoa = $dados["cliente"];
        } else {
            $cd_pessoa = '';
        }
        $SQL .="SELECT ";
        $SQL .="cd_pessoa, ";
        $SQL .="nm_pessoa, ";
        $SQL .="dt_nasc, ";
        $SQL .="TO_CHAR(dt_nasc, 'DD-MM') as data_aniversario, ";
        $SQL .="TO_CHAR(dt_ligacao, 'DD-MM-YYYY') as data_ligacao, ";
        $SQL .="TO_CHAR(dt_ligacao, 'YYYY') as ano_ligacao, ";
        $SQL .="cd_questionario, ";
        $SQL .="ds_questionario, ";
        $SQL .="mes, ";
        $SQL .="nome_atendente, ";
        $SQL .="dt_ligacao ";
        $SQL .="FROM vw_questionario_retorno ";
        $SQL .="WHERE dt_ligacao between '$per_ini_pesquisas' and '$per_fim_pesquisas' ";

        if (isset($dados['Relatorios']['pesquisa'])) {
            $pesquisa = '';
            foreach ($dados['Relatorios']['pesquisa'] as $key => $value) {
                $pesquisa .= ',' . $value;
            }
            $pesquisa = substr($pesquisa, 1);
            $SQL .= "AND cd_questionario IN($pesquisa) ";
        }
        if ($cd_pessoa != null) {
            $SQL .= " AND cd_pessoa IN ($cd_pessoa) ";
        }

        $SQL .="ORDER BY ds_questionario, ";
        $SQL .="ano_ligacao, ";
        $SQL .="mes, ";
        $SQL .="data_ligacao, ";
        $SQL .="data_aniversario, ";
        $SQL .="nm_pessoa ";
        return $this->query($SQL);
    }

    public function retorno_contato_pesquisa($dados) {

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

        $per_ini_envio = $dados["Relatorios"]["per_ini_envio"];
        $per_fim_envio = $dados["Relatorios"]["per_fim_envio"];

        $per_ini_retorno = $dados["Relatorios"]["per_ini_retorno"];
        $per_fim_retorno = $dados["Relatorios"]["per_fim_retorno"];

        if (empty($per_ini_envio)) {
            $per_ini_envio = '1990-01-01';
        } else {
            $per_ini_envio = $funcionalidades->formatarDataBd($per_ini_envio);
        }
        if ($per_fim_envio == null) {
            $per_fim_envio = date("Y-m-d");
        } else {
            $per_fim_envio = $funcionalidades->formatarDataBd($per_fim_envio);
        }

        if (empty($per_ini_retorno)) {
            $per_ini_retorno = '1990-01-01';
        } else {
            $per_ini_retorno = $funcionalidades->formatarDataBd($per_ini_retorno);
        }
        if ($per_fim_retorno == null) {
            $per_fim_retorno = date("Y-m-d");
        } else {
            $per_fim_retorno = $funcionalidades->formatarDataBd($per_fim_retorno);
        }

        if ($dados["Relatorios"]['clientes'] == 'E') {
            $cd_pessoa = $dados["cliente"];
        } else {
            $cd_pessoa = '';
        }
        $SQL = "";
        $SQL .="select ";
        $SQL .="cd_questionario, ";
        $SQL .="ds_questionario, ";
        $SQL .="cd_pessoa, ";
        $SQL .="nm_pessoa, ";
        $SQL .="status_atendimento, ";
        $SQL .="nr_pl, ";
        $SQL .="cd_ped, ";
        $SQL .="valor_vendido, ";
        $SQL .="mes, ";
        $SQL .="dt_ligacao, ";
        $SQL .="nm_usu, ";
        $SQL .="dt_compra, ";
        $SQL .="TO_CHAR(dt_compra, 'DD-MM-YYYY') as data_compra, ";
        $SQL .="TO_CHAR(dt_ligacao, 'DD-MM-YYYY') as data_ligacao ";
        $SQL .="from vw_questionario_atendimento_retorno ";
        $SQL .="WHERE dt_ligacao between '$per_ini_envio' and '$per_fim_envio' ";
        $SQL .= "AND dt_compra BETWEEN '$per_ini_retorno' AND '$per_fim_retorno' ";
        if (isset($dados['Relatorios']['pesquisa'])) {
            $pesquisa = '';
            foreach ($dados['Relatorios']['pesquisa'] as $key => $value) {
                $pesquisa .= ',' . $value;
            }
            $pesquisa = substr($pesquisa, 1);
            $SQL .= "AND cd_questionario IN($pesquisa) ";
        }
        if ($cd_pessoa != null) {
            $SQL .= " AND cd_pessoa IN ($cd_pessoa) ";
        }
        $SQL .="ORDER BY ds_questionario, ";
        $SQL .="dt_ligacao, ";
        $SQL .="dt_compra, ";
        $SQL .="nm_pessoa ";
        return $this->query($SQL);
    }

    public function resposta_pesquisa($dados) {

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
        if (empty($dados['Relatorios']['per_ini_pesquisas'])) {
            $per_ini_pesquisas = date('Y-m-d');
        } else {
            $per_ini_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios']['per_ini_pesquisas']);
        }
        if (empty($dados['Relatorios']['per_fim_pesquisas'])) {
            $per_fim_pesquisas = date('Y-m-d');
        } else {
            $per_fim_pesquisas = $funcionalidades->formatarDataBd($dados['Relatorios'][
                    'per_fim_pesquisas']);
        }

        if ($dados["Relatorios"]['clientes'] == 'E') {
            $cd_pessoa = $dados["cliente"];
        } else {
            $cd_pessoa = '';
        }

        $SQL .="SELECT ";
        $SQL .="cd_pessoa, ";
        $SQL .="nm_pessoa, ";
        $SQL .="cd_questionario, ";
        $SQL .="ds_questionario, ";
        $SQL .="ds_pergunta, ";
        $SQL .="ds_pergunta_cpl, ";
        $SQL .="TO_CHAR(dt_cad, 'DD-MM-YYYY') as data_atendimento, ";
        $SQL .="nm_atendente, ";
        $SQL .="protocolo ";
        $SQL .="FROM vw_questionario_atendimento_respostas ";
        $SQL .="WHERE dt_cad between '$per_ini_pesquisas' and '$per_fim_pesquisas' ";

        if (isset($dados['Relatorios']['pesquisa'])) {
            $pesquisa = '';
            foreach ($dados['Relatorios']['pesquisa'] as $key => $value) {
                $pesquisa .= ',' . $value;
            }
            $pesquisa = substr($pesquisa, 1);
            $SQL .= "AND cd_questionario IN($pesquisa) ";
        }
        if ($cd_pessoa != null) {
            $SQL .= " AND cd_pessoa IN ($cd_pessoa) ";
        }

        $SQL .="ORDER BY ds_questionario, ";
        $SQL .="dt_cad, ";
        $SQL .="ds_pergunta, ";
        $SQL .="nm_pessoa, ";
        $SQL .="nm_atendente ";
        return $this->query($SQL);
    }

    /**
     * Relatório de Estoque Detalhado por Família/Grupo
     * Retorna dados de estoque agrupados por família ou grupo com valores e percentuais
     * 
     * @param array $parametros Array com: dt_referencia, cd_filial, tipo_agrupamento, ordenacao, exibir_estoque_zerado
     * @return array Dados do relatório
     */
    public function estoque_detalhado($parametros) {
        $dt_referencia = $parametros['dt_referencia'];
        $cd_filial = $parametros['cd_filial'];
        $tipo_agrupamento = $parametros['tipo_agrupamento']; // 'FAMILIA' ou 'GRUPO'
        $ordenacao = $parametros['ordenacao']; // 'VALOR_DESC', 'VALOR_ASC', 'QTDE_DESC', 'QTDE_ASC', 'NOME'
        $exibir_estoque_zerado = $parametros['exibir_estoque_zerado'];

        // Define campo de agrupamento
        $campo_agrupamento = ($tipo_agrupamento == 'FAMILIA') ? 'familia.ds_familia' : 'grupo.ds_grupo';
        $campo_agrupamento_alias = ($tipo_agrupamento == 'FAMILIA') ? 'ds_categoria' : 'ds_categoria';
        $tabela_agrupamento = ($tipo_agrupamento == 'FAMILIA') ? 'est_produto_familia familia' : 'est_produto_grupo grupo';
        $join_campo = ($tipo_agrupamento == 'FAMILIA') ? 'familia.cd_familia = prod.cd_familia' : 'grupo.cd_grupo = prod.cd_grupo';

        $SQL = "
        WITH totais_gerais AS (
            SELECT 
                SUM(est.qtde_estoque * est.vlr_custo_gerenc) as total_valor_geral,
                SUM(est.qtde_estoque) as total_qtde_geral
            FROM est_produto_cpl_tamanho_prc_filial_estoque est
            INNER JOIN est_produto_cpl_tamanho tam ON tam.cd_cpl_tamanho = est.cd_cpl_tamanho
            INNER JOIN est_produto prod ON prod.cd_produto = tam.cd_produto
            WHERE est.cd_filial IN ($cd_filial)
            AND est.qtde_estoque " . ($exibir_estoque_zerado ? ">=" : ">") . " 0
        )
        SELECT 
            " . $campo_agrupamento . " as ds_categoria,
            SUM(est.qtde_estoque * est.vlr_custo_gerenc)::NUMERIC(14,2) as custo_total,
            SUM(est.qtde_estoque)::NUMERIC(14,2) as qtde_total,
            COUNT(DISTINCT prod.cd_produto) as total_skus,
            CASE 
                WHEN totais.total_qtde_geral > 0 
                THEN (SUM(est.qtde_estoque) / totais.total_qtde_geral * 100)::NUMERIC(14,2)
                ELSE 0 
            END as perc_qtde,
            CASE 
                WHEN totais.total_valor_geral > 0 
                THEN (SUM(est.qtde_estoque * est.vlr_custo_gerenc) / totais.total_valor_geral * 100)::NUMERIC(14,2)
                ELSE 0 
            END as perc_valor
        FROM est_produto_cpl_tamanho_prc_filial_estoque est
        INNER JOIN est_produto_cpl_tamanho tam ON tam.cd_cpl_tamanho = est.cd_cpl_tamanho
        INNER JOIN est_produto prod ON prod.cd_produto = tam.cd_produto
        INNER JOIN " . $tabela_agrupamento . " ON " . $join_campo . "
        CROSS JOIN totais_gerais totais
        WHERE est.cd_filial IN ($cd_filial)
        AND est.qtde_estoque " . ($exibir_estoque_zerado ? ">=" : ">") . " 0
        GROUP BY " . $campo_agrupamento . ", totais.total_valor_geral, totais.total_qtde_geral";

        // Adiciona ordenação
        switch ($ordenacao) {
            case 'VALOR_ASC':
                $SQL .= " ORDER BY custo_total ASC";
                break;
            case 'QTDE_DESC':
                $SQL .= " ORDER BY qtde_total DESC";
                break;
            case 'QTDE_ASC':
                $SQL .= " ORDER BY qtde_total ASC";
                break;
            case 'NOME':
                $SQL .= " ORDER BY ds_categoria ASC";
                break;
            case 'VALOR_DESC':
            default:
                $SQL .= " ORDER BY custo_total DESC";
                break;
        }

        return $this->query($SQL);
    }

    /**
     * Busca detalhes de subcategorias para o relatório de estoque detalhado
     * Usado para expandir uma categoria principal e mostrar suas subcategorias
     * 
     * @param array $parametros Array com: dt_referencia, cd_filial, tipo_agrupamento, categoria_pai
     * @return array Dados das subcategorias
     */
    public function estoque_detalhado_subcategorias($parametros) {
        $dt_referencia = $parametros['dt_referencia'];
        $cd_filial = $parametros['cd_filial'];
        $tipo_agrupamento = $parametros['tipo_agrupamento'];
        $categoria_pai = $parametros['categoria_pai'];

        // Para esta versão inicial, retornamos array vazio
        // Pode ser expandido futuramente para mostrar detalhes por tamanho/cor dentro de cada família
        return array();
    }

}
