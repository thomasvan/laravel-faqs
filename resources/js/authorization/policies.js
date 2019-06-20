export default {
    modify(user, model) {
        return user.id == model.id;
    },
    accept(user, answer) {
        return user.id == answer.user.id;
    }
};
