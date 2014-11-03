(function() {

var app = angular.module('question', []);


app.directive("questionDisplay", function() {
    return {
        restrict: 'E',
        templateUrl: 'question-display.html',
        controller: function() {
            this.text = question.text;
            this.image = "images/" + question.image;

            this.showText = (question.type & 1) == 1;
            this.showImage = (question.type & 2) == 2;

            switch (question.type) {
                case 1: this.qtypeString = "question-text"; break;
                case 2: this.qtypeString = "question-image"; break;
                case 3: this.qtypeString = "question-both"; break;
            }

        },
        controllerAs: 'question'
    };
});


app.directive("formWrapper", function() {
    return {
        restrict: 'E',
        templateUrl: 'question-form.html'
    };
});

app.controller("SubmitAnswerController", function() {
    this.answer = "";
    this.submit = function () {
        alert("Your answer is " + this.answer);
    };
});

app.controller("TauntController", function() {
    this.get = function () {
        return taunts[0];
    };
});

var question = {
    type: 3,
    id: 123,
    text: "Who am i?",
    image: '0Wcc.png'
};

var taunts = [
    "Are you sure??",
    "May I lock it?",
    "Double check!!",
    "Easy, aint it?",
    "Very peculiar!"
];

})();