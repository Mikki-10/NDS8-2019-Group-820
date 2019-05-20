# Makefile for NDS Custom Shell ###  ###  ###  ###  ###  ###  ###

CC=g++
BIN=ndsShell
DEV=devShell
LIBS=-lldap -lldapcpp
FLAGS=-std=c++14 -Wall -Wextra -pedantic

# ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  Sources

MDIR=src/main.cpp
SHDIR=src/shell/Shell.cpp src/shell/Terminal.cpp
LCDIR=src/ldap/LDAPClient.cpp
LGDIR=src/logging/Logger.cpp

SRC=$(MDIR) $(SHDIR) $(LCDIR) $(LGDIR)

# ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###

all:
	$(CC) -o $(BIN) $(SRC) $(FLAGS) $(LIBS)

debug:
	$(CC) -o $(BIN) $(SRC) $(FLAGS) -DDEBUG $(LIBS)

dev:
	$(CC) -o $(DEV) $(SRC) $(FLAGS) -DDEBUG $(LIBS)

clean:
	rm -rf $(BIN) $(DEV)

deploy:
	sudo cp ./$(BIN) /bin/$(BIN)

# ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###  ###
