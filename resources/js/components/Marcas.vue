<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <!-- Início Card de Buscas -->
                <div class="card mb-3">
                    <div class="card-header">Busca de Marcas</div>

                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md mb-3">
                                <input-container-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID da marca.">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idlHelp">
                                </input-container-component>
                            </div>

                            <div class="col-md mb-3">
                                <input-container-component titulo="Nome da marca" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca.">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp">
                                </input-container-component>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-sm float-right">Pesquisar</button>
                    </div>
                </div>
                <!-- Fim Card de Buscas -->

                <!-- Início do Card de Listagem de Marcas -->
                <div class="card">
                    <div class="card-header">Relação de Marcas</div>

                    <div class="card-body">
                        <table-component></table-component>
                    </div>

                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-sm float-right">Adicionar</button>
                    </div>
                </div>
                <!-- Fim do Card de Listagem de Marcas -->

            </div>
        </div>
    </div>
</template>

<script>

</script>
