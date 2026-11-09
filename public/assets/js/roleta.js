
/*adiciona os elementos da roleta*/
const bigomons = [
    { label: "Pikachu" },
    { label: "Charizard" },
    { label: "Bulbasaur" },
    { label: "Squirtle" },
    { label: "Gengar" },
    { label: "Mewtwo" }
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

        //gira a roleta até a opção sorteada
        roleta.spinToItem(indice, 4000, true, 3, 1);

        setTimeout(function () {
            const nomePokemon = bigomons[indice].label;
            resultado.textContent = "Seu Bigomon é: " + nomePokemon + "!";

            pop(
                true,
                "Seu Bigomon é...",
                nomePokemon,
                "/inicioLogin/kyogre.webp",
                "Parabéns! Você ganhou o " + nomePokemon + ".",
                "Continuar",
                "?modulo=mapa&acao=mapaInicio"
            );
        }, 4200);
    });
}
