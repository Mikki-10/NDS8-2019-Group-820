# Custom shell for Telenor project

* Made by Pierre
* Modified by Andrej

#### Shell Functionality

* What should the shell allow? what should be forbidden?
  * current codebase allows altering any linux command to do what we want

* Choices on how to execute linux commands
  * Exec [die.net](<https://linux.die.net/man/3/execvp>), [stackoverflow](<https://stackoverflow.com/questions/27541910/how-to-use-execvp>)
  * System [some site](<https://www.geeksforgeeks.org/system-call-in-c/>)

* Do we want some advanced mechanics, as command history?
  * [SO](<https://stackoverflow.com/questions/8435923/getting-arrow-keys-from-cin>)

#### LDAP

* phpLDAPadmin: https://192.168.20.4:8443

* Some good-looking query
  ![](readme_img/query_001.png)

* Result from this query:

  ![](readme_img/query_output.png)

* Result from the custom shell

![](readme_img/shell_output.png)



#### Some links

* [Using CLion for remote development](<https://www.youtube.com/watch?v=g1zPcja3zAU>)
  * [Manual](<https://blog.jetbrains.com/clion/2018/09/initial-remote-dev-support-clion/>), [Another manual](<https://www.jetbrains.com/help/clion/remote-projects-support.html>)
  * [Cmake: -lldap](<https://stackoverflow.com/questions/34625627/how-to-link-to-the-c-math-library-with-cmake>)
* LDAPc++
  * [example](<https://github.com/openldap/openldap/blob/master/contrib/ldapc%2B%2B/examples/main.cpp>)
  * [repository](<https://github.com/openldap/openldap/tree/master/contrib/ldapc%2B%2B>)
  * installed on LDAP server (192.168.20.4), using git clone and following the installation
* LDAP documentation
  * [Index](<https://linux.die.net/man/3/ldap>), [Extended Synchronous LDAP Search](<https://linux.die.net/man/3/ldap_search_ext_s>) - shouldn't be required anymore