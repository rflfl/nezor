/**
 * Composable para aplicar máscaras em inputs.
 * Suporta telefone brasileiro, CPF, CNPJ e máscaras customizadas.
 *
 * Uso:
 * const { maskPhone, maskCpf, maskCnpj, maskDate, maskCustom, unmask } = useMask();
 *
 * <input v-model="phone" @input="e => phone = maskPhone(e.target.value)" />
 */
export function useMask() {
    /**
     * Remove todos os caracteres não numéricos.
     */
    function unmask(value) {
        if (!value) return '';
        return String(value).replace(/\D/g, '');
    }

    /**
     * Máscara de telefone brasileiro: (99) 99999-9999
     * Aceita fixo (10 dígitos) ou celular (11 dígitos).
     */
    function maskPhone(value) {
        const numbers = unmask(value);
        if (numbers.length <= 10) {
            return numbers
                .replace(/(\d{0,2})/, '($1')
                .replace(/(\(\d{2})(\d{0,4})/, '$1) $2')
                .replace(/(\(\d{2}\) \d{4})(\d{0,4})/, '$1-$2');
        }
        return numbers
            .replace(/(\d{0,2})/, '($1')
            .replace(/(\(\d{2})(\d{0,5})/, '$1) $2')
            .replace(/(\(\d{2}\) \d{5})(\d{0,4})/, '$1-$2');
    }

    /**
     * Máscara de CPF: 999.999.999-99
     */
    function maskCpf(value) {
        const numbers = unmask(value).slice(0, 11);
        return numbers
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})/, '$1-$2');
    }

    /**
     * Máscara de CNPJ: 99.999.999/9999-99
     */
    function maskCnpj(value) {
        const numbers = unmask(value).slice(0, 14);
        return numbers
            .replace(/(\d{2})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1/$2')
            .replace(/(\d{4})(\d{1,2})/, '$1-$2');
    }

    /**
     * Máscara inteligente de CPF/CNPJ.
     * Até 11 dígitos aplica CPF, a partir de 12 aplica CNPJ.
     */
    function maskCpfCnpj(value) {
        const numbers = unmask(value);
        if (numbers.length > 11) {
            return maskCnpj(value);
        }
        return maskCpf(value);
    }

    /**
     * Máscara de data: 99/99/9999
     */
    function maskDate(value) {
        const numbers = unmask(value).slice(0, 8);
        return numbers
            .replace(/(\d{2})(\d)/, '$1/$2')
            .replace(/(\d{2})(\d)/, '$1/$2');
    }

    /**
     * Máscara customizada via padrão.
     * Ex: maskCustom(value, '999.999.999-99')
     */
    function maskCustom(value, pattern) {
        const numbers = unmask(value);
        let result = '';
        let numIndex = 0;

        for (let i = 0; i < pattern.length && numIndex < numbers.length; i++) {
            if (pattern[i] === '9') {
                result += numbers[numIndex];
                numIndex++;
            } else {
                result += pattern[i];
            }
        }

        return result;
    }

    return {
        unmask,
        maskPhone,
        maskCpf,
        maskCnpj,
        maskCpfCnpj,
        maskDate,
        maskCustom,
    };
}
