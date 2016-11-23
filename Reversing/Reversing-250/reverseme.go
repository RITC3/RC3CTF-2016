package main

import (
	"bytes"
	"fmt"
	"github.com/alexmullins/zip"
	"io"
	"io/ioutil"
	"log"
	"os"
)

func main() {
	if len(os.Args) < 2 {
		fmt.Println("Specify the file to package as argv1, will overwrite, existing")
		os.Exit(1)
	}
	enc := []byte{0x0e, 0x06, 0x05, 0x08, 0x07, 0x0e, 0x44, 0x06, 0x1b, 0x44, 0x0b, 0x1c, 0x1a, 0x1d}
	s := step1(enc)
	buf := step2(os.Args[1], s)
	seed := step3(s)
	step4(buf, seed)
	ioutil.WriteFile("flag.enc", buf, 0644)
}

/*//make password

 */

func step1(enc []byte) string {
	for i := 0; i < len(enc); i++ {
		enc[i] ^= 0x69
	}
	return string(enc)
}

/*//decrypt zip
func step3(buf []byte, pass string) {
	reader := bytes.NewReader(buf)
	r, _ := zip.NewReader(reader, int64(len(buf)))
	for _, f := range r.File {
		f.SetPassword(pass)
		fmt.Printf("Contents of %s:\n", f.Name)
		fmt.Println(f.IsEncrypted())
		rc, err := f.Open()
		if err != nil {
			log.Fatal(err)
		}
		_, err = io.CopyN(os.Stdout, rc, 68)
		if err != nil {
			log.Fatal(err)
		}
		rc.Close()
		fmt.Println()
	}
}*/

func step2(filename, pass string) []byte {
	f, err := os.Open(filename)
	if err != nil {
		log.Fatal(err)
	}
	buf := new(bytes.Buffer)
	w := zip.NewWriter(buf)
	er, err := w.Encrypt(filename, pass)
	if err != nil {
		log.Fatal(err)
	}
	_, err = io.Copy(er, f)
	if err != nil {
		log.Fatal(err)
	}
	w.Close()
	f.Close()
	return buf.Bytes()
}

//bogus seed
func step3(pass string) int {
	seed := 0
	for len(pass) < 5 {
		if seed == 5 {
			seed++
		} else {
			seed ^= len(pass) + 7
		}
	}
	return seed
}

func step4(buf []byte, seed int) {
	for i := 0; i < len(buf)-1; i++ {
		buf[i] = (buf[i] - buf[i+1] + byte(seed)) & 0xff
	}
}

/*//decrypt file
func dec(buf []byte) {
	for i := len(buf) - 1; i > 0; i-- {
		buf[i-1] = (buf[i] + buf[i-1]) & 0xff
	}
}*/
