
/*adiciona os elementos da roleta*/
const bigomons = [
    { label: "Torchic", value: 255 },
    { label: "Mudkip", value: 258 },
    { label: "Treecko", value: 252 },
    { label: "Poochyena", value: 261 },
    { label: "Zigzagoon", value: 263 },
    { label: "Lotad", value: 270 },
    { label: "Seedot", value: 273 },
    { label: "Tailow", value: 276 },
    { label: "Wingull", value: 278 },
    { label: "Ralts", value: 280 },
    { label: "Surskit", value: 283 },
    { label: "Shroomish", value: 285 },
    { label: "Slakoth", value: 287 },
    { label: "Makuhita", value: 296 },
    { label: "Aron", value: 304 },
    { label: "Meditite", value: 307 },
    { label: "Electrike", value: 309 },
    { label: "Carvanha", value: 318 },
    { label: "Numel", value: 322 },
    { label: "Trapinch", value: 328 },
    { label: "Cacnea", value: 331 },
    { label: "Swablu", value: 333 },
    { label: "Corphish", value: 341 },
    { label: "Shuppet", value: 353 },
    { label: "Duskull", value: 355 },
    { label: "Snorunt", value: 361 },
    { label: "Bagon", value: 371 },
    { label: "Beldum", value: 374 }
];

/*puxa os elementos do HTML pelos seus IDs */
const container = document.getElementById("roleta");
const botao = document.getElementById("girar");
const resultado = document.getElementById("resultado");

/*constante para configurar a roleta em si*/
const opcoes = {
    items: bigomons,
    isInteractive: false,
    itemBackgroundColors: [
        "#7c3aed", "#db2777", "#0891b2",
        "#ea580c", "#16a34a", "#ca8a04"
    ],
    itemLabelColors: ["#ffffff"],
    itemLabelFontSizeMax: 16,
    borderColor: "#ffffff",
    borderWidth: 3
};

/*verifica se os elementos e a biblioteca foram carregados*/
if (!container || !botao || !resultado) {
    console.error("Não foi possível encontrar os elementos da roleta ou do botão.");
} else if (typeof spinWheel === "undefined") {
    resultado.textContent = "Erro ao carregar a biblioteca da roleta.";
} else {

    //cria a roleta usando as opções definidas
    const roleta = new spinWheel.Wheel(container, opcoes);

    /*sorteia quando clicado no botão girar*/
    botao.addEventListener("click", function () {
        resultado.textContent = "Sorteando...";
        botao.disabled = true;

        //escolhe um índice aleatório do array de pokemons
        const indice = Math.floor(Math.random() * bigomons.length);
        
        const imgUrl = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-viii/brilliant-diamond-shining-pearl/${bigomons[indice].value}.webp`;

        //gira a roleta até a opção sorteada
        roleta.spinToItem(indice, 4000, true, 3, 1);

        setTimeout(function () {
            const nomePokemon = bigomons[indice].label;
            resultado.textContent = "Seu Bigomon é: " + nomePokemon + "!";

            pop(
                false,
                "Seu Bigomon é...",
                nomePokemon,
                imgUrl,
                "Parabéns! Você ganhou o " + nomePokemon + ".",
                "Continuar",
                "?modulo=mapa&acao=mapaInicio"
            );
        }, 4200);
    });
}
