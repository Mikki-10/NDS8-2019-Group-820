#include <sys/wait.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <ldap.h>
#include <sys/time.h>

// https://linux.die.net/man/3/ldap

/*
  Function Declarations for builtin shell commands:
 */
//int lsh_cd(char **args);
int lsh_execute(char **args);
int lsh_help(char **args);
int lsh_exit(char **args);
int lsh_1(char **args);
int lsh_2(char **args);

void ldap_debug(LDAP *ptr) {

    // https://linux.die.net/man/3/ldap_set_option

    printf("LDAP Handler:\n");
    int res;
    if (LDAP_OPT_SUCCESS == ldap_get_option(ptr, LDAP_OPT_PROTOCOL_VERSION, &res)) {
        printf("\tProtocol version: %d\n", res);
    }
    printf("\n");

}

void emergency_exit(int ldap_error_number) {
    // I need (ldap *) here
    // https://linux.die.net/man/3/ldap_error
    char *print_result = ldap_err2string(ldap_error_number);
    printf("LDAP Error: %s\n", print_result);
}

/*
  List of builtin commands, followed by their corresponding functions.
 */
char *builtin_str[] = {
        "help",
        "exit",
        "1",
        "2"
};

int (*builtin_func[]) (char **) = {
//  &lsh_cd,
        &lsh_help,
        &lsh_exit,
        &lsh_1,
        &lsh_2
};

int lsh_num_builtins() {
    return sizeof(builtin_str) / sizeof(char *);
}

/*
  Builtin function implementations.
*/
//int lsh_cd(char **args)
//{
//  if (args[1] == NULL) {
//    fprintf(stderr, "lsh: expected argument to \"cd\"\n");
//  } else {
//    if (chdir(args[1]) != 0) {
//      perror("lsh");
//    }
//  }
//  return 1;
//}

int lsh_1(char **args)
{
    printf("You are going to be redirected to System A");
    FILE *f = popen( "ssh -t -t nds@192.168.40.1 -p 50222", "r" );
    return 1;
}

int lsh_2(char **args)
{
    printf("You are going to be redirected to System B");
    return 1;
}

int lsh_help(char **args)
{
    int i;
    printf("Test Shell\n");
    printf("Please type the number corresponding to the system you want to access, and hit enter.\n");
    printf("The following are built in:\n");

    for (i = 0; i < lsh_num_builtins(); i++) {
        printf("  %s\n", builtin_str[i]);
    }

    printf("Use the man command for information on other programs.\n");
    return 1;
}

int lsh_exit(char **args)
{
    return 0;
}


int lsh_launch(char **args)
{
    pid_t pid, wpid;
    int status;

    pid = fork();
    if (pid == 0) {
        // Child process
        if (execvp(args[0], args) == -1) {
            perror("lsh");
        }
        exit(EXIT_FAILURE);
    } else if (pid < 0) {
        // Error forking
        perror("lsh");
    } else {
        // Parent process
        do {
            wpid = waitpid(pid, &status, WUNTRACED);
        } while (!WIFEXITED(status) && !WIFSIGNALED(status));
    }

    return 1;
}


#define LSH_TOK_BUFSIZE 64
#define LSH_TOK_DELIM " \t\r\n\a"
char **lsh_split_line(char *line)
{
    int bufsize = LSH_TOK_BUFSIZE, position = 0;
    char **tokens = malloc(bufsize * sizeof(char*));
    char *token;

    if (!tokens) {
        fprintf(stderr, "lsh: allocation error\n");
        exit(EXIT_FAILURE);
    }

    token = strtok(line, LSH_TOK_DELIM);
    while (token != NULL) {
        tokens[position] = token;
        position++;

        if (position >= bufsize) {
            bufsize += LSH_TOK_BUFSIZE;
            tokens = realloc(tokens, bufsize * sizeof(char*));
            if (!tokens) {
                fprintf(stderr, "lsh: allocation error\n");
                exit(EXIT_FAILURE);
            }
        }

        token = strtok(NULL, LSH_TOK_DELIM);
    }
    tokens[position] = NULL;
    return tokens;
}


//char *lsh_read_line(void)
//{
//  char *line = NULL;
//  ssize_t bufsize = 0; // have getline allocate a buffer for us
//  getline(&line, &bufsize, stdin);
//  return line;
//}


#define LSH_RL_BUFSIZE 1024
char *lsh_read_line(void)
{
    int bufsize = LSH_RL_BUFSIZE;
    int position = 0;
    char *buffer = malloc(sizeof(char) * bufsize);
    int c;

    if (!buffer) {
        fprintf(stderr, "lsh: allocation error\n");
        exit(EXIT_FAILURE);
    }

    while (1) {
        // Read a character
        c = getchar();

        // If we hit EOF, replace it with a null character and return.
        if (c == EOF || c == '\n') {
            buffer[position] = '\0';
            return buffer;
        } else {
            buffer[position] = c;
        }
        position++;

        // If we have exceeded the buffer, reallocate.
        if (position >= bufsize) {
            bufsize += LSH_RL_BUFSIZE;
            buffer = realloc(buffer, bufsize);
            if (!buffer) {
                fprintf(stderr, "lsh: allocation error\n");
                exit(EXIT_FAILURE);
            }
        }
    }
}


void lsh_loop(void)
{
    char *line;
    char **args;
    int status;

    do {
        printf("Please type the number corresponding to the system you want to connect to\n");
        printf("1.A\n");
        printf("2.B\n");
        printf(">");
        line = lsh_read_line();
        args = lsh_split_line(line);
        status = lsh_execute(args);

        free(line);
        free(args);
    } while (status);
}

int lsh_execute(char **args)
{
    int i;

    if (args[0] == NULL) {
        // An empty command was entered.
        return 1;
    }

    for (i = 0; i < lsh_num_builtins(); i++) {
        if (strcmp(args[0], builtin_str[i]) == 0) {
            return (*builtin_func[i])(args);
        }
    }

    return lsh_launch(args);
}

int main(int argc, char **argv)
{
    // Load config files, if any.
    // Run command loop.

    printf("{\n");


    LDAP *ldap_ptr;

    {   // Step 1: Initialise the handler object (LDAP *) and bind to server
        ldap_initialize(&ldap_ptr, "ldap://127.0.0.1:389");
        int ldap_version = 3;
        int res;

        res = ldap_set_option(ldap_ptr, LDAP_OPT_PROTOCOL_VERSION, &ldap_version);
        if (LDAP_OPT_SUCCESS != res) emergency_exit(res);

        // https://linux.die.net/man/3/ldap_bind
        res = ldap_simple_bind_s(ldap_ptr, "cn=readonly,dc=example,dc=org", "readonly");
        if (LDAP_SUCCESS != res) emergency_exit(res);
    }

    {   // Step 2: Make a query
        int scope = LDAP_SCOPE_BASE;
        int msgid = ldap_search(ldap_ptr, getlogin(), scope, "system=*" , NULL, 1);



        //    struct timeval *timeout;
        //    timeout=NULL;
        //    int resulttype;
        //    resulttype = ldap_result(ldap,msgid, 1, timeout,result);
        //    LDAPMessage *first_message;
        //    first_message=ldap_first_message(ldap,*result);
        //    int *errcodep;
        //    char **matcheddnp;
        //    char **errmsgp;
        //    char ***referralsp;
        //    LDAPControl ***serverctrlsp;
        //    int freeit;
        //    int parsed_result;
        //    parsed_result = ldap_parse_result(ldap,*result,errcodep,matcheddnp,errmsgp,referralsp,serverctrlsp,freeit);
        //    printf("%d",parsed_result);
    }
    printf("}\n");

    // lsh_loop();
    return EXIT_SUCCESS;
}

