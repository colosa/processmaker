#!groovy
node {
    /**
    * Branch should be in gitflow format.  If not, then we'll abort.
    */
    if(!env.BRANCH_NAME.matches(/(feature|hotfix|bugfix|release|master|develop)\/.+/) && !env.BRANCH_NAME.matches(/^PR-.*$/)) {
      hipchatSend message: "${env.BRANCH_NAME} Build: Does not match gitflow naming. Aborted", room: 'engineering'
      error "Job does not follow gitflow naming format."
    }
    // Parse out our short name and potential jira ticket.  Null if not associated. If null, then for now we won't notify
    // on jira ticket
    def jiraTicket = env.BRANCH_NAME.find(/HOR-\d+/)

    def shortname = env.BRANCH_NAME.replace('/', '-').replace('.', '-').toLowerCase()
    def dbSuffix = shortname.replace('-', '')

    echo "Building for ${env.BRANCH_NAME}"

    // Checkout source
    checkout scm

    try {
         stage('Start Notification') {
            if(jiraTicket) {
                jiraComment issueKey: jiraTicket, body: "Build ${env.BUILD_NUMBER} Starting.\nTicket will be updated once build is completed.\n\n${env.BUILD_URL}"
            }
            hipchatSend message: "${env.BRANCH_NAME} Build: ${env.BUILD_NUMBER} Starting.\n${env.BUILD_URL}", room: 'engineering'
        }

        stage('Dependencies') {
            echo "Running Composer"
            sh 'composer install'
            echo "Running rake"
            sh 'rake'
        }

        stage('Generate QA MySQL Databases') {
            withCredentials([string(credentialsId: 'qa-rds-hostname', variable: 'rdsHostname'), usernamePassword(credentialsId: 'qa-rds-credentials', passwordVariable: 'rdsPassword', usernameVariable: 'rdsUsername')]) {
              echo 'Dropping existing database and recreating.'
              sh "mysql -h ${rdsHostname} -u ${rdsUsername} -p${rdsPassword} -e 'drop database if exists qa205${dbSuffix}; create database qa205${dbSuffix}'"
              sh "mysql -h ${rdsHostname} -u ${rdsUsername} -p${rdsPassword} -e 'drop database if exists qa300${dbSuffix}; create database qa300${dbSuffix}'"
            }
        }

        stage('Publish to QA-205') {
            sshagent(['processmaker-deploy']) {
                echo 'Dropping existing files and recreating'
                sh "ssh processmaker@build-qa205.processmaker.net 'rm -Rf /home/processmaker/${shortname}'"
                sh "scp -r ./ processmaker@build-qa205.processmaker.net:~/${shortname}"
                echo 'Creating necessary directories'
                sh "ssh processmaker@build-qa205.processmaker.net 'mkdir -p /home/processmaker/${shortname}/workflow/engine/js/labels'"
                 sh "ssh processmaker@build-qa205.processmaker.net 'mkdir -p /home/processmaker/${shortname}/workflow/public_html/translations'"
            }
        }

        stage('Publish to QA-300') {
             sshagent(['processmaker-deploy']) {
                echo 'Dropping existing files and recreating'
                sh "ssh processmaker@build-qa300.processmaker.net 'rm -Rf /home/processmaker/${shortname}'"
                sh "scp -r ./ processmaker@build-qa300.processmaker.net:~/${shortname}"
                echo 'Creating necessary directories'
                sh "ssh processmaker@build-qa300.processmaker.net 'mkdir -p /home/processmaker/${shortname}/workflow/engine/js/labels'"
                 sh "ssh processmaker@build-qa300.processmaker.net 'mkdir -p /home/processmaker/${shortname}/workflow/public_html/translations'"
            }
        }

        stage('Success Notification') {
            withCredentials([string(credentialsId: 'qa-rds-hostname', variable: 'rdsHostname'), usernamePassword(credentialsId: 'qa-rds-credentials', passwordVariable: 'rdsPassword', usernameVariable: 'rdsUsername')]) {
             if(jiraTicket) {
                jiraComment issueKey: jiraTicket, body: "" +
                    "Build ${env.BUILD_NUMBER} Completed.\n" +
                    "5.6 Build: https://${shortname}.qa205.processmaker.net\n" +
                    "Database Host: ${rdsHostname}\n" +
                    "Username: ${rdsUsername}\n" +
                    "Password: ${rdsPassword}\n" +
                    "Database: qa205${dbSuffix}\n\n" +
                    "7.0 Build: https://${shortname}.qa300.processmaker.net\n" +
                    "Database Host: ${rdsHostname}\n" +
                    "Username: ${rdsUsername}\n" +
                    "Password: ${rdsPassword}\n" +
                    "Database: qa300${dbSuffix}\n\n" +
                    "${env.BUILD_URL}"
             }
                hipchatSend room: 'engineering', message: "" +
                    "${env.BRANCH_NAME} Build: ${env.BUILD_NUMBER} Completed.\n" +
                    "${env.BUILD_URL}"
            }
        }
     } catch(error) {
         if(jiraTicket) {
           jiraComment issueKey: jiraTicket, body: "Build ${env.BUILD_NUMBER} Failed: ${error}\n\n${env.BUILD_URL}"
         }
         hipchatSend message: "${env.BRANCH_NAME} Build: ${env.BUILD_NUMBER} Failed: ${error}\n${env.BUILD_URL}", room: 'engineering'
     }
}