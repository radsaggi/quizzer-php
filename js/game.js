(function(){

var app = angular.module('njath', ['profile', 'question']);

app.directive("navBar", function(){
    return {
        restrict: 'E',
        templateUrl: 'nav-bar.html'
    };
});

app.controller("DisplayController", function() {
    this.PAGES = PAGES;
    this.showing = PAGES.QUESTION_PAGE;

    this.showingProfilePage = function() {
        return this.showing == PAGES.PROFILE_PAGE;
    };
    this.showingQuestionPage = function() {
        return this.showing == PAGES.QUESTION_PAGE;
    }


    this.showPage = function(page) {
        this.showing = page;
    };
});

var PAGES = {
    PROFILE_PAGE: 0,
    QUESTION_PAGE: 1
};

var userInfo = {name: "sunny" ,
    lscore: "450",
    tscore: "100",
    level: "3"
};

})();