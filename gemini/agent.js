const { GoogleGenerativeAI } = require("@google/generative-ai");
const { exec } = require("child_process");
const util = require("util");
const readline = require("readline");
const fs = require("fs");
const execPromise = util.promisify(exec);

const API_KEY = "AIzaSyAsUr3Bh8E7k0MuxNJ9nuqYq8xd_JvgXGg";
const genAI = new GoogleGenerativeAI(API_KEY);

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
});

async function runAgent() {
    const model = genAI.getGenerativeModel({
        model: "gemini-3-flash-preview",
        systemInstruction: `Tu es un expert Symfony et DevOps. Ton but est d'aider à créer ou modifier des projets Symfony.
        
        Règles strictes :
        1. Ne jamais utiliser d'étoiles (*) dans tes explications.
        2. Pour exécuter une commande (ex: composer, bin/console), écris : COMMANDE: suivi de la commande.
        3. Pour lire un fichier, demande à l'utilisateur ou propose une commande cat/type.
        4. Pour créer ou remplacer un fichier, utilise cette syntaxe sur une seule ligne :
           ECRIRE: chemin/du/fichier | CONTENU: le code ici
        5. Sois précis sur les namespaces Symfony.
        6. Le repertoire du projet se trouve dans D:/Sahaza
        `,
    });

    const askQuestion = (query) =>
        new Promise((resolve) => rl.question(query, resolve));

    console.log(
        "Agent Symfony prêt. Posez votre question ou demandez une modification.",
    );

    while (true) {
        const userPrompt = await askQuestion("\nVous : ");

        if (userPrompt.toLowerCase() === "quitter") break;

        try {
            const result = await model.generateContent(userPrompt);
            let response = result.response.text().replace(/\*/g, "");

            const lines = response.split("\n");

            for (let line of lines) {
                // Gestion des commandes classiques
                if (line.startsWith("COMMANDE:")) {
                    const cmd = line.replace("COMMANDE:", "").trim();
                    console.log("Exécution de : " + cmd);
                    const { stdout } = await execPromise(cmd, {
                        shell: "powershell.exe",
                    });
                    console.log("Résultat :\n" + stdout);
                }
                // Gestion de la création/modification de fichiers
                else if (line.startsWith("ECRIRE:")) {
                    const parts = line.split("| CONTENU:");
                    const filePath = parts[0].replace("ECRIRE:", "").trim();
                    const content = parts[1].trim();

                    fs.writeFileSync(filePath, content, "utf8");
                    console.log("Fichier modifié ou créé : " + filePath);
                } else {
                    if (line.trim() !== "") console.log("Gemini : " + line);
                }
            }
        } catch (e) {
            console.error("Erreur : " + e.message);
        }
    }
    process.exit(0);
}

runAgent();
