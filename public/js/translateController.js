angular.module('translate', [])
    .controller('TranslateController', function ($scope, $http, $filter) {
        $scope.currentProject = null;
        $scope.currentLanguage = null;
        $scope.isAdmin = false;
        $scope.strings = []
        $scope.topUsers = [];
        $scope.admins = [];

        $scope.loadTranslatedStrings = function () {
            $http.get('/api/strings?project_id=' + $scope.currentProject + '&language_id=' + $scope.currentLanguage).
                success(function (data, status, headers, config) {
                    angular.forEach(data, function (string) {
                        $scope.strings[string.base_string_id].push(string);
                    });
                }).
                error(function (data, status, headers, config) {
                    alert('Error ' + status + ' occured. Please try again.')
                });
        }

        $scope.loadInvolvedUsers = function () {
            $http.get('/api/strings/users?project_id=' + $scope.currentProject + '&language_id=' + $scope.currentLanguage).
                success(function (data, status, headers, config) {
                    $scope.topUsers = data
                }).
                error(function (data, status, headers, config) {
                    alert('Error ' + status + ' occured. Please try again.')
                });
        }

        $scope.loadAdmins = function () {
            $http.get('/api/strings/admins?project_id=' + $scope.currentProject + '&language_id=' + $scope.currentLanguage).
                success(function (data, status, headers, config) {
                    $scope.admins = data
                }).
                error(function (data, status, headers, config) {
                    alert('Error ' + status + ' occured. Please try again.')
                });
        }

        $scope.checkPrivileges = function () {
            $http.get('/api/check-privileges?language_id=' + $scope.currentLanguage).
                success(function (data, status, headers, config) {
                    $scope.isAdmin = data;
                }).
                error(function (data, status, headers, config) {
                    alert('Error ' + status + ' occured. Please try again.')
                });
        }

        $scope.loadData = function () {
            $scope.currentProject = $('#project').val();
            $scope.currentLanguage = $('#language').val();

            $http.get('/api/base-strings?project_id=' + $scope.currentProject).
                success(function (data, status, headers, config) {
                    $scope.baseStrings = data;

                    angular.forEach(data, function (string) {
                        $scope.strings[string.id] = [];
                    });

                    $scope.loadTranslatedStrings();
                    $scope.loadInvolvedUsers();
                    $scope.loadAdmins();
                    $scope.checkPrivileges();
                }).
                error(function (data, status, headers, config) {
                    alert('Error ' + status + ' occured. Please try again.')
                });
        }

        $scope.vote = function (base_string_id, string_id, vote) {
            var postData = {
                'string_id': string_id,
                'vote': vote
            };

            $http.post('/api/strings/vote', postData).
                success(function (data, status, headers, config) {
                    angular.forEach($scope.strings[base_string_id], function (string, key) {
                        if (string.id == string_id) {
                            if (vote == 1)
                                !isNaN($scope.strings[base_string_id][key].up_votes) ? $scope.strings[base_string_id][key].up_votes++ : $scope.strings[base_string_id][key].up_votes = 1;
                            else
                                !isNaN($scope.strings[base_string_id][key].down_votes) ? $scope.strings[base_string_id][key].down_votes++ : $scope.strings[base_string_id][key].down_votes = 1;
                        }
                    });
                }).
                error(function (data, status, headers, config) {
                    alert('Error ' + status + ' occured. ' + data.error.message)
                });
        }

        $scope.trash = function (base_string_id, string_id) {
            var postData = {
                'string_id': string_id,
                'language_id': $scope.currentLanguage
            };

            $http.post('/api/strings/trash', postData).
                success(function (data, status, headers, config) {
                    angular.forEach($scope.strings[base_string_id], function (string, key) {
                        if (string.id == string_id)
                            $scope.strings[base_string_id].splice(key, 1);
                    });
                }).
                error(function (data, status, headers, config) {
                    alert('Error ' + status + ' occured. Please try again.')
                });
        }

        $scope.accept = function (base_string_id, string_id) {
            var postData = {
                'string_id': string_id,
                'base_string_id': base_string_id,
                'language_id': $scope.currentLanguage
            };

            $http.post('/api/strings/accept', postData).
                success(function (data, status, headers, config) {
                    angular.forEach($scope.strings[base_string_id], function (string, key) {
                        if (string.id == string_id)
                            $scope.strings[base_string_id][key].is_accepted = true;
                        else
                            $scope.strings[base_string_id][key].is_accepted = false;
                    });
                }).
                error(function (data, status, headers, config) {
                    alert('Error ' + status + ' occured. Please try again.')
                });
        }

        $scope.add = function (base_string_id) {
            var textInput = $('#stringInput' + base_string_id);

            var postData = {
                'project_id': $scope.currentProject,
                'language_id': $scope.currentLanguage,
                'base_string_id': base_string_id,
                'text': textInput.val()
            };

            $http.post('/api/strings/store', postData).
                success(function (data, status, headers, config) {
                    $scope.strings[base_string_id].push(data);
                    textInput.val('');
                }).
                error(function (data, status, headers, config) {
                    alert('Error ' + status + ' occured. Please try again.')
                });
        }

        //$scope.hideAccepted = function () {
        //    console.log($scope.baseStrings)
        //    console.log($scope.strings)
        //    $scope.baseStrings = []
        //
        //    angular.forEach($scope.strings, function (string, key) {
        //        //$scope.strings[string.id] = [];
        //    });
        //}
    });
