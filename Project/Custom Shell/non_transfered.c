//
// Created by Andrej on 27.04.2019.
//

void ldap_debug(LDAP *ptr) {

    // https://linux.die.net/man/3/ldap_set_option
    printf("LDAP Handler:\n");
    int res;
    if (LDAP_SUCCESS == ldap_get_option(ptr, LDAP_OPT_PROTOCOL_VERSION, &res)) {
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


// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---


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


// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

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


// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

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

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

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

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

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
        if (LDAP_SUCCESS != res) emergency_exit(res);

        // https://linux.die.net/man/3/ldap_bind
        res = ldap_simple_bind_s(ldap_ptr, "cn=readonly,dc=example,dc=org", "readonly");
        if (LDAP_SUCCESS != res) emergency_exit(res);
    }

    {   // Step 2: Make a query
        int msgid = ldap_search(ldap_ptr, getlogin(), LDAP_SCOPE_BASE, "system=*" , NULL, 1);



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

