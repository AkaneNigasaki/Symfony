const { GoogleGenerativeAI } = require("@google/generative-ai");
const { exec } = require("child_process");
const util = require("util");
const readline = require("readline");
const fs = require("fs");
const execPromise = util.promisify(exec);

// REMPLACER PAR VOTRE CLE
const API_KEY = "AIzaSyAsUr3Bh8E7k0MuxNJ9nuqYq8xd_JvgXGg";
const genAI = new GoogleGenerativeAI(API_KEY);

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
});

async function runAgent() {
    // Utilisation de 1.5-flash qui a un quota beaucoup plus large (15 RPM)
    const model = genAI.getGenerativeModel({
        model: "gemini-1.5-flash",
        systemInstruction: `Tu es un expert Symfony et DevOps. Repertoire projet : D:/Sahaza.
        
        Règles :
        1. INTERDICTION d'utiliser des étoiles (*).
        2. Syntaxe commande : COMMANDE: suivi de la commande CMD.
        3. Syntaxe fichier : ECRIRE: chemin | CONTENU: code.
        4. Pour naviguer, utilise la syntaxe CMD : D: puis cd Sahaza.`,
    });

    const askQuestion = (query) =>
        new Promise((resolve) => rl.question(query, resolve));

    console.log(
        "Agent Symfony (CMD) pret. En attente de vos ordres sur D:/Sahaza...",
    );

    while (true) {
        const userPrompt = await askQuestion("\nVous : ");
        if (userPrompt.toLowerCase() === "quitter") break;

        try {
            const result = await model.generateContent(userPrompt);
            let response = result.response.text().replace(/\*/g, ""); // Suppression radicale des étoiles

            const lines = response.split("\n");

            for (let line of lines) {
                if (line.startsWith("COMMANDE:")) {
                    const cmd = line.replace("COMMANDE:", "").trim();
                    console.log("Execution CMD : " + cmd);
                    try {
                        // FORCE CMD.EXE ici
                        const { stdout, stderr } = await execPromise(cmd, {
                            shell: "cmd.exe",
                        });
                        if (stdout) console.log("Resultat :\n" + stdout);
                        if (stderr) console.log("Info :\n" + stderr);
                    } catch (cmdError) {
                        console.error("Erreur commande : " + cmdError.message);
                    }
                } else if (line.startsWith("ECRIRE:")) {
                    const parts = line.split("| CONTENU:");
                    const filePath = parts[0].replace("ECRIRE:", "").trim();
                    const content = parts[1].trim();
                    fs.writeFileSync(filePath, content, "utf8");
                    console.log("Fichier modifie : " + filePath);
                } else {
                    if (line.trim() !== "") console.log("Gemini : " + line);
                }
            }
        } catch (e) {
            if (e.message.includes("429")) {
                console.log("Quota Free atteint. Attente de 60s...");
                await new Promise((r) => setTimeout(r, 60000));
            } else {
                console.error("Erreur API : " + e.message);
            }
        }
    }
    process.exit(0);
}

runAgent();
