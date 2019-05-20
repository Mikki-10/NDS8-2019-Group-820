cmake_minimum_required(VERSION 3.10)
project(NDStest)

set(CMAKE_CXX_STANDARD 14)

add_executable(
    NDStest

    src/header.hpp
    src/shell/CritSys.h
    src/shell/ShellCommand.h

    src/main.cpp
    src/shell/Shell.cpp src/shell/Shell.h
    src/ldap/LDAPClient.cpp src/ldap/LDAPClient.h
    src/logging/Logger.cpp src/logging/Logger.h
    src/shell/Terminal.cpp src/shell/Terminal.h
)

target_link_libraries(NDStest ldap)
target_link_libraries(NDStest ldapcpp)
