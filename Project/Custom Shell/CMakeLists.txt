cmake_minimum_required(VERSION 3.10)
project(NDStest)

set(CMAKE_CXX_STANDARD 14)

add_executable(
    NDStest
    main.cpp header.hpp
    Shell.cpp Shell.h
    LDAPClient.cpp LDAPClient.h
    ShellCommand.h
)

# target_link_libraries(NDStest ldap)
