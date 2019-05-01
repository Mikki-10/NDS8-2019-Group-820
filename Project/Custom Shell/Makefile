# Makefile for NDS Custom Shell ###  ###  ###  ###  ###  ###  ###

CC=g++
BIN=custshell
SRC=src/main.cpp src/shell/Shell.cpp src/ldap/LDAPClient.cpp
LIBS=-lldap -lldapcpp
FLAGS=-std=c++14 -Wall -Wextra -pedantic

# ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###

all:
	$(CC) -o $(BIN) $(SRC) $(FLAGS) $(LIBS)

debug:
	$(CC) -o $(BIN) $(SRC) $(FLAGS) -DDEBUG $(LIBS)
clean:
	rm -rf $(BIN)

# ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###
