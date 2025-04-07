import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static values = {
        slug: String,
        index: Number,
        total: Number
    }

    connect() {
        console.log("🎯 Contrôleur Quiz Stimulus activé !");
    }

    submit(event) {
        event.preventDefault();
        console.log("✅ Soumission interceptée par Stimulus");

        const form = event.target;
        const formData = new FormData(form);
        const answerId = formData.get("answer");
        const questionId = form.dataset.questionId;

        fetch(`/quiz/${this.slugValue}/answer`, {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: new URLSearchParams({
                answer_id: answerId,
                question_id: questionId
            })
        })
        .then(response => {
            if (!response.ok) throw new Error("Erreur lors de l'enregistrement");
            return response.text();
        })
        .then(() => {
            this.loadNextQuestion();
        })
        .catch(error => console.error(error));
    }

    loadNextQuestion() {
        const nextIndex = this.indexValue + 1;
        if (nextIndex >= this.totalValue) {
            window.location.href = `/quiz/${this.slugValue}/results`;
            return;
        }

        fetch(`/quiz/${this.slugValue}/q/${nextIndex}`)
        .then(response => response.text())
        .then(html => {
            this.element.innerHTML = html;
            this.indexValue = nextIndex;
        })
        .catch(error => console.error(error));
    }
}
