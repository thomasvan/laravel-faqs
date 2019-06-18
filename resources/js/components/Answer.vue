<script>
export default {
  props: ["answer"],
  data() {
    return {
      editing: false,
      body: this.answer.body,
      bodyHtml: this.answer.body_html,
      id: this.answer.id,
      questionId: this.answer.question_id
    };
  },
  methods: {
    update() {
      axios
        .patch(`/questions/${this.questionId}/answers/${this.id}`, {
          body: this.body
        })
        .then(res => {
          this.editing = false;
          this.bodyHtml = res.data.body_html;
          alert(res.data.message);
        })
        .catch(err => {
          console.log("Something went wrong!");
        });
    }
  }
};
</script>
