<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <!-- Início Card de Buscas -->
                <card-component titulo="Busca de Marcas">
                    <template v-slot:conteudo>
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
                    </template>
                    
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Pesquisar</button>
                    </template>
                </card-component>
                <!-- Fim Card de Buscas -->

                <!-- Início do Card de Listagem de Marcas -->
                <card-component titulo="Relação de Marcas">
                    <template v-slot:conteudo>
                        <table-component></table-component>
                    </template>

                    <template v-slot:rodape>
                        <button type="button" class="btn btn-primary btn-sm float-right"
                            data-toggle="modal" data-target="#modalMarca">Adicionar</button>
                    </template>
                </card-component>
                <!-- Fim do Card de Listagem de Marcas -->

            </div>
        </div>

        <modal-component id="modalMarca" titulo="Adicionar Marca">
            <template v-slot:conteudo>
                <input-container-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Opcional. Informe o nome da marca.">
                    <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" v-model="nomeMarca">
                </input-container-component>
                {{ nomeMarca }}
                <input-container-component titulo="Imagem" id="novoImagem" id-help="novoImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG">
                    <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" @change="carregarImagem($event)">
                </input-container-component>
                {{ arquivoImagem }}
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>        

    </div>
</template>

<script>
    export default {
        data() {
            return {
                urlBase: 'http://localhost:8000/api/v1/marca',
                nomeMarca: '',
                arquivoImagem: [],
            }
        },
        methods: {
            carregarImagem(e) {
                this.arquivoImagem = e.target.files
            },
            salvar() {
                console.log(this.nomeMarca, this.arquivoImagem[0])

                let formData = new FormData()
                formData.append('nome', this.nomeMarca)
                formData.append('imagem', this.arquivoImagem[0])

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                    }
                }

                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        console.log(response)
                    })
                    .catch(errors => {
                        console.log(errors)
                    })
            }
        },
    }
</script>
