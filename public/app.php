<?php
    //criando a classe com todos valores a serem mostrados na pagina inicial
    class Dashboard{
        public $data_inicio;
        public $data_fim;
        public $num_vendas;
        public $total_vendas;
        public $total_clientes_ativos;
        public $total_clientes_inativos;
        public $total_despesas;
        public $total_reclamacoes;
        public $total_elogios;
        public $total_sugestoes;

        public function __get($name){
            return $this->$name;
        }
        public function __set($name, $value){
            $this->$name=$value;
            return $this;//retorna true or false
        }
    }

    //classe para se conectar com o BD
    class Conexao{
        private $host = 'localhost';
        private $dbname = 'db_dashboard';
        private $user = 'root';
        private $pass = '';

        public function conectar(){
            try{
                $conexao = new PDO("mysql:host=$this->host;dbname=$this->dbname;", "$this->user", "$this->pass");
                $conexao->exec('set charset utf8');
                return $conexao;
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }
    }

    //classe para modificar os dados do BD, inserindo, atualizando, recuperando etc
    class Bd{
        private $conexao;
        private $dashboard;

        //um construtor que cria as duas classes ao ser instanciado
        public function __construct(Conexao $conexao, Dashboard $dashboard){
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        public function getNumVendas(){
            $query = 'select count(*) as numero_vendas from tb_vendas where data_venda between :data_inicio and :data_fim';
            $stmt = $this->conexao->prepare($query);//verifica a consulta
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();//executa a consulta

            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;//retorna o valor do numero de vendas
        }
        public function getTotalVendas(){
            $query = 'select sum(total) as total_vendas from tb_vendas where data_venda between :data_inicio and :data_fim';
            $stmt = $this->conexao->prepare($query);//verifica a consulta
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();//executa a consulta
            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;//retorna o valor do total de vendas
        }
        public function getClientesAtivos(){
            $query = 'select count(*) as total_clientes_ativos from tb_clientes where cliente_ativo = 1';
            $stmt = $this->conexao->prepare($query);//verifica a consulta
            $stmt->execute();//executa a consulta
            return $stmt->fetch(PDO::FETCH_OBJ)->total_clientes_ativos;//retorna o valor do total de clientes ativos
        }
        public function getClientesInativos(){
            $query = 'select count(*) as total_clientes_inativos from tb_clientes where cliente_ativo = 0';
            $stmt = $this->conexao->prepare($query);//verifica a consulta
            $stmt->execute();//executa a consulta
            return $stmt->fetch(PDO::FETCH_OBJ)->total_clientes_inativos;//retorna o valor do total de clientes inativos
        }
        public function getTotalDespesas(){
            $query = 'select sum(total) as total_despesas from tb_despesas where data_despesa between :data_inicio and :data_fim';
            $stmt = $this->conexao->prepare($query);//verifica a consulta
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();//executa a consulta
            return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;//retorna o valor do total de despesas
        }
        public function getTotalReclamacoes(){
            $query = 'select count(*) as total_reclamacoes from tb_contatos where tipo_contato = 1';
            $stmt = $this->conexao->prepare($query);//verifica a consulta
            $stmt->execute();//executa a consulta
            return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacoes;//retorna o valor do total de reclamacoes
        }
        public function getTotalElogios(){
            $query = 'select count(*) as total_elogios from tb_contatos where tipo_contato = 2';
            $stmt = $this->conexao->prepare($query);//verifica a consulta
            $stmt->execute();//executa a consulta
            return $stmt->fetch(PDO::FETCH_OBJ)->total_elogios;//retorna o valor do total de elogios
        }
        public function getTotalSugestoes(){
            $query = 'select count(*) as total_sugestoes from tb_contatos where tipo_contato = 3';
            $stmt = $this->conexao->prepare($query);//verifica a consulta
            $stmt->execute();//executa a consulta
            return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestoes;//retorna o valor do total de sugestoes
        }
    }


    $dashboard = new Dashboard();//intancia da classe dashboard
    $conexao = new Conexao();//intancia da classe de conexao com o BD

    $competencia = explode('-', $_GET['competencia']);//separa o mes e o ano da competencia enviada via GET por -
    $ano = $competencia[0];//atribui o primeiro valor separado para o ano
    $mes = $competencia[1];//atribui o segundo valor separado para o mes
    $dia = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);//função que retorna a quantidade de dias do mes

    //seta as datas dos atributos da classe dashboard para sem pesquisadas dentro do BD
    $dashboard->__set('data_inicio', $ano.'-'.$mes.'-'.'01');
    $dashboard->__set('data_fim', $ano.'-'.$mes.'-'.$dia);

    //instancia da classe que modifica os dados do BD
    $bd = new Bd($conexao, $dashboard);

    //seta o valor das variaveis da classe dashboard pelos valores recuperados pelos métodos que acessam o BD 
    $dashboard->__set('num_vendas', $bd->getNumVendas());
    $dashboard->__set('total_vendas', $bd->getTotalVendas());
    $dashboard->__set('total_clientes_ativos', $bd->getClientesAtivos());
    $dashboard->__set('total_clientes_inativos', $bd->getClientesInativos());
    $dashboard->__set('total_despesas', $bd->getTotalDespesas());
    $dashboard->__set('total_reclamacoes', $bd->getTotalReclamacoes());
    $dashboard->__set('total_elogios', $bd->getTotalElogios());
    $dashboard->__set('total_sugestoes', $bd->getTotalSugestoes());

    echo json_encode($dashboard);//transformando de array pra json object
?>